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
