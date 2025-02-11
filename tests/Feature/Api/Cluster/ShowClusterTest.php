<?php

use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to find a cluster', function () {
    $model = new Cluster;

    $states = Cluster::factory()->createMany(2)->pluck('id')->toArray();

    $cluster = Cluster::factory()->create();

    $response = $this->getJson(route('api.clusters.show', $cluster->id));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($model->getFillable());

    $stateId = $response->json()['id'];
    $foundStateKey = array_search($stateId, $states);

    expect($stateId)
        ->toBe($cluster->id)
        ->and($foundStateKey)
        ->toBeFalse();
});
