<?php

use App\Models\City;
use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to make a city active', function () {
    $model = new City();
    $city = City::factory()->create(['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.cities.set-active-status', ['city' => $city->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $city->id,
        ...$payload,
    ]);
});

test('it should return not found when trying to set status of a city that does not exist', function () {
    $payload = [
        'is_active' => false,
    ];

    $response = $this->putJson(route('api.cities.update', ['city' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\City] -1');
});

test('it should turn cluster relation inative when city is inactive', function () {
    $model = new City();
    $city = City::factory()->create(['is_active' => true]);
    $cluster = Cluster::factory()->create(['is_active' => true]);
    $city->clusters()->attach([$cluster->id], ['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.cities.set-active-status', ['city' => $city->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $city->id,
        ...$payload,
    ]);

    $this->assertDatabaseHas('cluster_city_pivot', [
        'cluster_id' => $cluster->id,
        'city_id' => $city->id,
        'is_active' => false,
    ]);
});
