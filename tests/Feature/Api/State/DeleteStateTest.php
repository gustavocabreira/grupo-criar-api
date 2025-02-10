<?php

use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to delete a state', function() {
    $model = new State;
    $state = State::factory()->create();

    $response = $this->deleteJson(route("api.states.destroy", ['state' => $state->id]));

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing($model->getTable(), $state->toArray());
});

test('it should return not found when trying to delete a state that does not exists', function() {
    $model = new State;

    $state = State::factory()->create();

    $response = $this->deleteJson(route("api.states.destroy", ['state' => -1]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\State] -1');

    $this->assertDatabaseHas($model->getTable(), $state->toArray());
});
