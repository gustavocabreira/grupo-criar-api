<?php

use App\Models\City;
use Illuminate\Http\Response;

test('it should be able to find a city', function () {
    $model = new City;

    $cities = City::factory()->createMany(2)->pluck('id')->toArray();

    $city = City::factory()->create();

    $response = $this->getJson(route('api.cities.show', $city->id));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($model->getFillable());

    $cityId = $response->json()['id'];
    $foundStateKey = array_search($cityId, $cities);

    expect($cityId)
        ->toBe($city->id)
        ->and($foundStateKey)
        ->toBeFalse();
});

test('it should return not found when trying to find a city that does not exist', function () {
    $response = $this->getJson(route('api.cities.show', -1));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\City] -1');

});
