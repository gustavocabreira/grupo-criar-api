<?php

use App\Models\City;
use App\Models\Cluster;
use App\Models\State;
use Illuminate\Http\Response;

test('it should sync cities with a cluster', function () {
    $state = State::factory()->create();
    $cities = City::factory()->count(5)->create(['state_id' => $state->id]);
    $cluster = Cluster::factory()->create();

    $cluster->cities()->attach($cities->pluck('id')->toArray(), ['is_active' => true]);

    $newCities = City::factory()->count(2)->create(['state_id' => $state->id]);

    $payload = [
        'cities' => $newCities->pluck('id')->toArray(),
    ];

    $response = $this->putJson(route('api.clusters.cities.update', ['cluster' => $cluster->id]), $payload);
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseCount('cluster_city_pivot', 7);

    foreach ($cities as $city) {
        $this->assertDatabaseHas('cluster_city_pivot', [
            'cluster_id' => $cluster->id,
            'city_id' => $city->id,
            'is_active' => false,
        ]);
    }

    foreach ($newCities as $city) {
        $this->assertDatabaseHas('cluster_city_pivot', [
            'cluster_id' => $cluster->id,
            'city_id' => $city->id,
            'is_active' => true,
        ]);
    }
});

dataset('invalid_payload', [
    'empty cities' => [
        ['cities' => []], ['cities' => 'The cities field is required.'],
    ],
    'city that does not exist' => [
        ['cities' => [-1]], ['cities.0' => 'The selected cities.0 is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to sync cities with a cluster with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    City::factory()->create();

    $cluster = Cluster::factory()->create();

    $response = $this->postJson(route('api.clusters.cities.destroy', ['cluster' => $cluster->id]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $response->assertJsonValidationErrors($key);

    $response->assertJsonFragment([
        'errors' => [
            $key[0] => [$expectedErrors[$key[0]]],
        ],
    ]);

    if (! empty($payload['cities'])) {
        $this->assertDatabaseMissing('cluster_city_pivot', [
            'city_id' => $payload['cities'][0],
        ]);
    }

    $this->assertDatabaseCount('cluster_city_pivot', 0);
})->with('invalid_payload');

test('it should set the older relation is_active to false when trying to sync cities that already has a cluster with a new cluster', function () {
    $oldCluster = Cluster::factory()->create();
    $newCluster = Cluster::factory()->create();

    $state = State::factory()->create();
    $city = City::factory()->create(['state_id' => $state->id]);
    $anotherCity = City::factory()->create();

    $oldCluster->cities()->attach([$city->id, $anotherCity->id], ['is_active' => true]);

    $payload = [
        'cities' => [$city->id],
    ];

    $response = $this->putJson(route('api.clusters.cities.update', ['cluster' => $newCluster->id]), $payload);
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas('cluster_city_pivot', [
        'cluster_id' => $oldCluster->id,
        'city_id' => $city->id,
        'is_active' => false,
    ]);

    $this->assertDatabaseHas('cluster_city_pivot', [
        'cluster_id' => $newCluster->id,
        'city_id' => $city->id,
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('cluster_city_pivot', [
        'cluster_id' => $oldCluster->id,
        'city_id' => $anotherCity->id,
        'is_active' => true,
    ]);
});
