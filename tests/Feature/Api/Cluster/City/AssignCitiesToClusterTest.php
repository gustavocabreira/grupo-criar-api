<?php

use App\Models\City;
use App\Models\Cluster;
use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to assign cities to a cluster', function () {
    $state = State::factory()->create();
    $cities = City::factory()->count(3)->create(['state_id' => $state->id])->pluck('id')->toArray();
    $cluster = Cluster::factory()->create();
    $payload = [
        'cities' => $cities,
    ];

    $response = $this->postJson(route('api.clusters.assign-cities', ['cluster' => $cluster->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    foreach ($cities as $city) {
        $this->assertDatabaseHas('cluster_city_pivot', [
            'cluster_id' => $cluster->id,
            'city_id' => $city,
        ]);
    }

    $this->assertDatabaseCount('cluster_city_pivot', 3);
});

dataset('invalid_payload', [
    'empty cities' => [
        ['cities' => []], ['cities' => 'The cities field is required.'],
    ],
    'city that does not exist' => [
        ['cities' => [-1]], ['cities.0' => 'The selected cities.0 is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to assign a new city to a cluster with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $city = City::factory()->create();

    $cluster = Cluster::factory()->create();

    $response = $this->postJson(route('api.clusters.assign-cities', ['cluster' => $cluster->id]), $payload);

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

test('it should set the previous city x cluster is_active as false when assigning a city to a new cluster', function () {
    $oldCluster = Cluster::factory()->create();
    $newCluster = Cluster::factory()->create();

    $city = City::factory()->create();
    $oldCluster->cities()->attach($city, ['is_active' => true]);

    $payload = [
        'cities' => [$city->id],
    ];

    $response = $this->postJson(route('api.clusters.assign-cities', ['cluster' => $newCluster->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

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
});

test('it should create only one record in cluster_city_pivot when passing duplicate city IDs', function () {
    $cluster = Cluster::factory()->create();
    $city = City::factory()->create();

    $payload = ['cities' => [$city->id, $city->id]];

    $response = $this->postJson(route('api.clusters.assign-cities', ['cluster' => $cluster->id]), $payload);
    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseCount('cluster_city_pivot', 1);

    $this->assertDatabaseHas('cluster_city_pivot', [
        'cluster_id' => $cluster->id,
        'city_id' => $city->id,
        'is_active' => true,
    ]);
});
