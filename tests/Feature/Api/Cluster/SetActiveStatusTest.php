<?php

use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to make a cluster active', function () {
    $model = new Cluster;
    $cluster = Cluster::factory()->create(['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.clusters.set-active-status', ['cluster' => $cluster->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $cluster->id,
        ...$payload,
    ]);
});
