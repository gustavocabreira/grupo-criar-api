<?php

use App\Models\Campaign;
use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should sync clusters with a campaign', function () {
    $campaign = Campaign::factory()->create();
    $clusters = Cluster::factory()->count(5)->create();

    $campaign->clusters()->attach($clusters->pluck('id')->toArray(), ['is_active' => true]);

    $newClusters = Cluster::factory()->count(2)->create();

    $payload = [
        'clusters' => $newClusters->pluck('id')->toArray(),
    ];

    $response = $this->putJson(route('api.campaigns.clusters.update', ['campaign' => $campaign->id]), $payload);
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseCount('cluster_campaign_pivot', 7);

    foreach ($clusters as $cluster) {
        $this->assertDatabaseHas('cluster_campaign_pivot', [
            'campaign_id' => $campaign->id,
            'cluster_id' => $cluster->id,
            'is_active' => false,
        ]);
    }

    foreach ($newClusters as $cluster) {
        $this->assertDatabaseHas('cluster_campaign_pivot', [
            'campaign_id' => $campaign->id,
            'cluster_id' => $cluster->id,
            'is_active' => true,
        ]);
    }
});
