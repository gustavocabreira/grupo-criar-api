<?php

use App\Models\State;
use Illuminate\Http\Response;

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
