<?php

use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to update a cluster', function () {
    $model = new Cluster();
    $cluster = Cluster::factory()->create();
    $payload = Cluster::factory()->make()->toArray();

    $response = $this->putJson(route('api.clusters.update', ['cluster' => $cluster->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $cluster->id, ...$payload]);
});

test('it should return not found when trying to update a cluster that does not exist', function () {
    $payload = Cluster::factory()->make()->toArray();

    $response = $this->putJson(route('api.clusters.update', ['cluster' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Cluster] -1');
});
