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
