<?php

use App\Models\Campaign;
use Illuminate\Http\Response;

test('it should be able to create a new campaign', function () {
    $model = new Campaign();

    $payload = Campaign::factory()->make()->toArray();

    $response = $this->postJson(route('api.campaigns.store'), $payload);
    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
});
