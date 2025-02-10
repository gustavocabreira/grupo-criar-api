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
