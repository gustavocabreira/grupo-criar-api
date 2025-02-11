<?php

use App\Models\City;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to create a new city', function () {
    $table = new City();
    $payload = City::factory()->make()->toArray();

    $response = $this->postJson(route('api.cities.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($table->getFillable());

    $this->assertDatabaseHas($table->getTable(), $payload);
    $this->assertDatabaseCount($table->getTable(), 1);
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

test('it should return unprocessable entity when trying to create a new city with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new City();

    $response = $this->postJson(route('api.cities.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');
