<?php

use App\Models\State;
use Illuminate\Http\Response;

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
