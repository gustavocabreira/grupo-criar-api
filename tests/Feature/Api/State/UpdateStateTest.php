<?php

use App\Models\State;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to update a state', function () {
    $model = new State;

    $state = State::factory()->create();
    $payload = State::factory()->make(['acronym' => $state->acronym])->toArray();

    $response = $this->putJson(route('api.states.update', ['state' => $state->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $state->id, ...$payload]);
});

test('it should return not found when trying to update a state that does not exist', function () {
    $payload = State::factory()->make()->toArray();

    $response = $this->putJson(route('api.states.update', ['state' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\State] -1');
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'empty acronym' => [
        ['acronym' => ''], ['acronym' => 'The acronym field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'acronym with more or less than 2 characters' => [
        ['acronym' => Str::repeat('*', 3)], ['acronym' => 'The acronym field must be 2 characters.'],
        ['acronym' => Str::repeat('*', 1)], ['acronym' => 'The acronym field must be 2 characters.'],
    ],
]);

test('it should return unprocessable entity when trying to create a new state with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new State;

    $state = State::factory()->create();

    $response = $this->putJson(route('api.states.update', ['state' => $state->id]), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
})->with('invalid_payload');

test('it should return the acronym has been taken when trying to update a state with an existing acronym', function () {
    $model = new State;
    $otherState = State::factory()->create();
    $state = State::factory()->create(['acronym' => 'AB']);

    $payload = [
        'name' => fake()->name(),
        'acronym' => $otherState->acronym,
    ];

    $response = $this->putJson(route('api.states.update', ['state' => $state->id]), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors(['acronym']);

    $response->assertJsonPath('errors.acronym.0', 'The acronym has already been taken.');
    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 2);
});
