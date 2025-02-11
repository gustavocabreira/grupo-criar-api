<?php

use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to delete a cluster', function () {
    $model = new Cluster;
    $cluster = Cluster::factory()->create();

    $response = $this->deleteJson(route('api.clusters.destroy', ['cluster' => $cluster->id]));

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing($model->getTable(), $cluster->toArray());
});

test('it should return not found when trying to delete a cluster that does not exists', function () {
    $model = new Cluster;

    $cluster = Cluster::factory()->create();

    $response = $this->deleteJson(route('api.clusters.destroy', ['cluster' => -1]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Cluster] -1');

    $this->assertDatabaseHas($model->getTable(), $cluster->toArray());
});
