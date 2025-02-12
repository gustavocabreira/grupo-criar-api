<?php

use App\Models\Campaign;
use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to remove a cluster from a campaign', function () {
    $campaign = Campaign::factory()->create();
    $clusters = Cluster::factory()->count(3)->create();

    $campaign->clusters()->attach($clusters->pluck('id')->toArray(), ['is_active' => true]);

    $payload = [
        'clusters' => [$clusters->first()->id],
    ];

    $response = $this->postJson(route('api.campaigns.remove-clusters', ['campaign' => $campaign->id]), $payload);
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseCount('cluster_campaign_pivot', 3);
    $this->assertDatabaseHas('cluster_campaign_pivot', [
        'campaign_id' => $campaign->id,
        'cluster_id' => $clusters->first()->id,
        'is_active' => false,
    ]);

    foreach ($clusters->slice(1) as $remainingCluster) {
        $this->assertDatabaseHas('cluster_campaign_pivot', [
            'campaign_id' => $campaign->id,
            'cluster_id' => $remainingCluster->id,
            'is_active' => true,
        ]);
    }
});

test('it should be able to remove multiple clusters from a campaign', function () {
    $campaign = Campaign::factory()->create();
    $clusters = Cluster::factory()->count(3)->create();

    $campaign->clusters()->attach($clusters->pluck('id')->toArray(), ['is_active' => true]);

    $payload = [
        'clusters' => [$clusters->first()->id, $clusters->last()->id],
    ];

    $response = $this->postJson(route('api.campaigns.remove-clusters', ['campaign' => $campaign->id]), $payload);
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    foreach ($payload as $cluster) {
        $this->assertDatabaseHas('cluster_campaign_pivot', [
            'campaign_id' => $campaign->id,
            'cluster_id' => $cluster,
            'is_active' => false,
        ]);
    }

    $this->assertDatabaseHas('cluster_campaign_pivot', [
        'campaign_id' => $campaign->id,
        'cluster_id' => $clusters[1]->id,
        'is_active' => true,
    ]);
});

dataset('invalid_payload', [
    'empty clusters' => [
        ['clusters' => []], ['clusters' => 'The clusters field is required.'],
    ],
    'cluster that does not exist' => [
        ['clusters' => [-1]], ['clusters.0' => 'The selected clusters.0 is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to remove a cluster from a company with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);

    $campaign = Campaign::factory()->create();

    $response = $this->postJson(route('api.campaigns.remove-clusters', ['campaign' => $campaign->id]), $payload);

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
