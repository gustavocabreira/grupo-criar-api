<?php

use App\Models\City;
use Illuminate\Http\Response;

test('it should be able to make a city active', function () {
    $model = new City;
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
