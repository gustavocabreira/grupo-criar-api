<?php

use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to find a state', function () {
    $model = new State();

    $states = State::factory()->createMany(2)->pluck('id')->toArray();

    $state = State::factory()->create();

    $response = $this->getJson(route('api.states.show', $state->id));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($model->getFillable());

    $stateId = $response->json()['id'];
    $foundStateKey = array_search($stateId, $states);

    expect($stateId)
        ->toBe($state->id)
        ->and($foundStateKey)
        ->toBeFalse();
});

test('it should return not found when trying to find a state that does not exist', function () {
    $response = $this->getJson(route('api.states.show', 99));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\State] 99');

});
