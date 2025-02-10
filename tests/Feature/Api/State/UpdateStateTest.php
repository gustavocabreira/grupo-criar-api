<?php

use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to update a state', function () {
    $model = new State();

    $state = State::factory()->create();
    $updatedState = State::factory()->make(['acronym' => $state->acronym])->toArray();

    $response = $this->putJson(route('api.states.update', ['state' => $state->id]), $updatedState);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $state->id, ...$updatedState]);
});
