<?php

use App\Models\City;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to update a city', function () {
    $model = new City();
    $city = City::factory()->create();
    $payload = City::factory()->make()->toArray();

    $response = $this->putJson(route('api.cities.update', ['city' => $city->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $city->id, ...$payload]);
});

test('it should return not found when trying to update a city that does not exist', function () {
    $payload = City::factory()->make()->toArray();

    $response = $this->putJson(route('api.cities.update', ['city' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\City] -1');
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'empty state_id' => [
        ['state_id' => ''], ['state_id' => 'The state id field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'state id that does not exist' => [
        ['state_id' => -1], ['state_id' => 'The selected state id is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to update a city with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new City;

    $city = City::factory()->create();

    $response = $this->putJson(route('api.cities.update', ['city' => $city->id]), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
})->with('invalid_payload');
