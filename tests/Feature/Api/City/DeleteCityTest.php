<?php

use App\Models\City;
use Illuminate\Http\Response;

test('it should be able to delete a city', function () {
    $model = new City;
    $city = City::factory()->create();

    $response = $this->deleteJson(route('api.cities.destroy', ['city' => $city->id]));

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing($model->getTable(), $city->toArray());
});

test('it should return not found when trying to delete a city that does not exists', function () {
    $model = new City;

    $city = City::factory()->create();

    $response = $this->deleteJson(route('api.cities.destroy', ['city' => -1]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\City] -1');

    $this->assertDatabaseHas($model->getTable(), $city->toArray());
});
