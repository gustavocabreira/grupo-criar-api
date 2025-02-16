<?php

use App\Models\Campaign;
use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to assign clusters to a campaign', function () {
    $clusters = Cluster::factory()->count(2)->create();
    $campaign = Campaign::factory()->create();

    $payload = [
        'clusters' => $clusters->pluck('id')->toArray(),
    ];

    $response = $this->postJson(route('api.campaigns.assign-clusters', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    foreach ($clusters as $cluster) {
        $this->assertDatabaseHas('cluster_campaign_pivot', [
            'cluster_id' => $cluster->id,
            'campaign_id' => $campaign->id,
        ]);
    }

    $this->assertDatabaseCount('cluster_campaign_pivot', 2);
});

dataset('invalid_payload', [
    'empty clusters' => [
        ['clusters' => []], ['clusters' => 'The clusters field is required.'],
    ],
    'cluster that does not exist' => [
        ['clusters' => [-1]], ['clusters.0' => 'The selected clusters.0 is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to assign a new cluster to a campaign with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    Cluster::factory()->create();

    $campaign = Campaign::factory()->create();

    $response = $this->postJson(route('api.campaigns.assign-clusters', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $response->assertJsonValidationErrors($key);

    $response->assertJsonFragment([
        'errors' => [
            $key[0] => [$expectedErrors[$key[0]]],
        ],
    ]);

    if (! empty($payload['clusters'])) {
        $this->assertDatabaseMissing('cluster_campaign_pivot', [
            'cluster_id' => $payload['clusters'][0],
        ]);
    }

    $this->assertDatabaseCount('cluster_campaign_pivot', 0);
})->with('invalid_payload');

test('it should set the previous cluster x campaign is_active as false when assigning a cluster to a new campaign', function () {
    $oldCampaign = Campaign::factory()->create();
    $newCampaign = Campaign::factory()->create();

    $cluster = Cluster::factory()->create();
    $oldCampaign->clusters()->attach([$cluster->id], ['is_active' => true]);

    $payload = [
        'clusters' => [$cluster->id],
    ];

    $response = $this->postJson(route('api.campaigns.assign-clusters', ['campaign' => $newCampaign->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas('cluster_campaign_pivot', [
        'campaign_id' => $oldCampaign->id,
        'cluster_id' => $cluster->id,
        'is_active' => false,
    ]);

    $this->assertDatabaseHas('cluster_campaign_pivot', [
        'campaign_id' => $newCampaign->id,
        'cluster_id' => $cluster->id,
        'is_active' => true,
    ]);
});

test('it should create only one record in cluster_campaign_pivot when passing duplicate cluster IDs', function () {
    $campaign = Campaign::factory()->create();
    $cluster = Cluster::factory()->create();

    $payload = ['clusters' => [$cluster->id, $cluster->id]];

    $response = $this->postJson(route('api.campaigns.assign-clusters', ['campaign' => $campaign->id]), $payload);
    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseCount('cluster_campaign_pivot', 1);

    $this->assertDatabaseHas('cluster_campaign_pivot', [
        'cluster_id' => $cluster->id,
        'campaign_id' => $campaign->id,
        'is_active' => true,
    ]);
});
