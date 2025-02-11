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

    $response = $this->postJson(route('api.clusters.cities.store', ['cluster' => $cluster->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    foreach ($cities as $city) {
        $this->assertDatabaseHas('cluster_city_pivot', [
            'cluster_id' => $cluster->id,
            'city_id' => $city,
        ]);
    }

    $this->assertDatabaseCount('cluster_city_pivot', 3);
});
