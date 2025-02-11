<?php

use App\Models\City;
use App\Models\Cluster;
use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to remove a city from a cluster', function () {
    $cluster = Cluster::factory()->create();
    $state = State::factory()->create();
    $city = City::factory()->count(3)->create(['state_id' => $state->id]);

    $cluster->cities()->attach($city->pluck('id')->toArray(), ['is_active' => true]);

    $payload = [
        'cities' => [$city[0]->id],
    ];

    $response = $this->deleteJson(route('api.clusters.cities.destroy', ['cluster' => $cluster->id]), $payload);
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseCount('cluster_city_pivot', 3);
    $this->assertDatabaseHas('cluster_city_pivot', [
        'cluster_id' => $cluster->id,
        'city_id' => $city[0]->id,
        'is_active' => false,
    ]);

    foreach ($city->slice(1) as $remainingCity) {
        $this->assertDatabaseHas('cluster_city_pivot', [
            'cluster_id' => $cluster->id,
            'city_id' => $remainingCity->id,
            'is_active' => true,
        ]);
    }
});

test('it should be able to remove multiple cities from a cluster', function () {
    $cluster = Cluster::factory()->create();
    $state = State::factory()->create();
    $cities = City::factory()->count(3)->create(['state_id' => $state->id]);

    $cluster->cities()->attach($cities->pluck('id')->toArray(), ['is_active' => true]);

    $payload = [
        'cities' => [$cities[0]->id, $cities[1]->id],
    ];

    $response = $this->deleteJson(route('api.clusters.cities.destroy', ['cluster' => $cluster->id]), $payload);
    $response->assertStatus(Response::HTTP_NO_CONTENT);

    foreach ($payload as $city) {
        $this->assertDatabaseHas('cluster_city_pivot', [
            'cluster_id' => $cluster->id,
            'city_id' => $city,
            'is_active' => false,
        ]);
    }

    $this->assertDatabaseHas('cluster_city_pivot', [
        'cluster_id' => $cluster->id,
        'city_id' => $cities[2]->id,
        'is_active' => true,
    ]);
});

dataset('invalid_payload', [
    'empty cities' => [
        ['cities' => []], ['cities' => 'The cities field is required.'],
    ],
    'city that does not exist' => [
        ['cities' => [-1]], ['cities.0' => 'The selected cities.0 is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to remove a city from a cluster with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $city = City::factory()->create();

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
