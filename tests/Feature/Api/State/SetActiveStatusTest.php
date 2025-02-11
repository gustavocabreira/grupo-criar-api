<?php

use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to make a state active', function () {
    $model = new State();
    $state = State::factory()->create(['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.states.set-active-status', ['state' => $state->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $state->id,
        ...$payload,
    ]);
});

test('it should return not found when trying to set status of a state that does not exist', function () {
    $payload = [
        'is_active' => false,
    ];

    $response = $this->putJson(route('api.states.update', ['state' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\State] -1');
});
