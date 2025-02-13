<?php

use App\Models\City;
use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to find a cluster', function () {
    $model = new Cluster();

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

test('it should return not found when trying to find a state that does not exist', function () {
    $response = $this->getJson(route('api.clusters.show', -1));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Cluster] -1');

});

test('it should return a cluster with its active cities', function () {
    $model = new Cluster();
    $cityModel = new City();

    $cluster = Cluster::factory()->create();
    $city = City::factory()->create();
    $cluster->cities()->attach([$city->id], ['is_active' => true]);

    $response = $this->getJson(route('api.clusters.show', [
        'cluster' => $cluster->id,
        'includes' => 'activeCities',
    ]));

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            ...$model->getFillable(),
            'active_cities' => [
                '*' => $cityModel->getFillable(),
            ],
        ]);

    expect($response->json()['active_cities'][0]['pivot']['is_active'])->toBe(1)
        ->and(count($response->json()['active_cities']))->toBe(1);
});

test('it should return a cluster with all its cities', function () {
    $model = new Cluster();
    $cityModel = new City();

    $cluster = Cluster::factory()->create();
    $city = City::factory()->create();
    $anotherCity = City::factory()->create();
    $cluster->cities()->attach([$city->id], ['is_active' => true]);
    $cluster->cities()->attach([$anotherCity->id], ['is_active' => false]);

    $response = $this->getJson(route('api.clusters.show', [
        'cluster' => $cluster->id,
        'includes' => 'cities',
    ]));

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            ...$model->getFillable(),
            'cities' => [
                '*' => $cityModel->getFillable(),
            ],
        ]);

    expect($response->json()['cities'][0]['pivot']['is_active'])->toBe(1)
        ->and(count($response->json()['cities']))->toBe(2)
        ->and($response->json()['cities'][1]['pivot']['is_active'])->toBe(0);
});
