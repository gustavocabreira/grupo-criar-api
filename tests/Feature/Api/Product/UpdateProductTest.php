<?php

use App\Models\Product;
use Illuminate\Http\Response;

test('it should be able to update a product', function () {
    $model = new Product();
    $product = Product::factory()->create();
    $payload = Product::factory()->make()->toArray();

    $response = $this->putJson(route('api.products.update', ['product' => $product->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $product->id, ...$payload]);
});

test('it should return not found when trying to update a product that does not exist', function () {
    $payload = Product::factory()->make()->toArray();

    $response = $this->putJson(route('api.products.update', ['product' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Product] -1');
});
