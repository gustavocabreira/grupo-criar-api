<?php

use App\Models\Product;
use Illuminate\Http\Response;

test('it should be able to delete a product', function () {
    $model = new Product();
    $product = Product::factory()->create();

    $response = $this->deleteJson(route('api.products.destroy', ['product' => $product->id]));

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing($model->getTable(), $product->toArray());
});

test('it should return not found when trying to delete a product that does not exists', function () {
    $model = new Product();

    $product = Product::factory()->create();

    $response = $this->deleteJson(route('api.products.destroy', ['product' => -1]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Product] -1');

    $this->assertDatabaseHas($model->getTable(), $product->toArray());
});
