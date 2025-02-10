<?php

use App\Models\City;
use Illuminate\Http\Response;

test('it should be able to update a city', function () {
    $model = new City();
    $city = City::factory()->create();
    $payload = City::factory()->make()->toArray();

    $response = $this->putJson(route('api.cities.update', ['city' => $city->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $city->id, ...$payload]);
});
