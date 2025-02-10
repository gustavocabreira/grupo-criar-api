<?php

use App\Models\State;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to create a new state', function() {
    $model = new State();

    $payload = State::factory()->make()->toArray();

    $response = $this->postJson(route('api.states.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.']
    ],
    'empty acronym' => [
        ['acronym' => ''], ['acronym' => 'The acronym field is required.']
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'acronym with more or less than 2 characters' => [
        ['acronym' => Str::repeat('*', 3)], ['acronym' => 'The acronym field must be 2 characters.'],
        ['acronym' => Str::repeat('*', 1)], ['acronym' => 'The acronym field must be 2 characters.']
    ],
]);

test('it should return unprocessable entity when trying to create a new state with an invalid payload', function($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);

    $model = new State();

    $response = $this->postJson(route('api.states.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');
