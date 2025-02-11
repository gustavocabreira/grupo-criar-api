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

    $response = $this->postJson(route('api.campaigns.clusters.store', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    foreach ($clusters as $cluster) {
        $this->assertDatabaseHas('cluster_campaign_pivot', [
            'cluster_id' => $cluster->id,
            'campaign_id' => $campaign->id,
        ]);
    }

    $this->assertDatabaseCount('cluster_campaign_pivot', 2);
});
