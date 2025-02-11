<?php

use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to create a new cluster', function() {
    $model = new Cluster;

    $payload = Cluster::factory()->make()->toArray();

    $response = $this->postJson(route('api.clusters.store'), $payload);
    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);

});
