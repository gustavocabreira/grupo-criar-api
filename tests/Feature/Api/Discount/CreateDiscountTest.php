<?php

use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to create a new discount', function () {
    $model = new Discount();

    $payload = Discount::factory()->make()->toArray();

    $response = $this->postJson(route('api.discounts.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
});
