<?php

use App\Models\Product;
use Illuminate\Http\Response;

test('it should be able to create a product', function () {
    $model = new Product();
    $payload = Product::factory()->make()->toArray();

    $response = $this->postJson(route('api.products.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
});
