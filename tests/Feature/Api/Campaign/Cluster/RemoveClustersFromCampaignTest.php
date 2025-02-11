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

    $response = $this->deleteJson(route('api.campaigns.clusters.destroy', ['campaign' => $campaign->id]), $payload);
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

    $response = $this->deleteJson(route('api.campaigns.clusters.destroy', ['campaign' => $campaign->id]), $payload);
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
