<?php

use App\Models\Product;
use Illuminate\Http\Response;

test('it should be able to find a product', function () {
    $model = new Product();

    $product = Product::factory()->create();

    $response = $this->getJson(route('api.products.show', $product->id));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($model->getFillable());

    $productId = $response->json()['id'];

    expect($productId)->toBe($product->id);
});

test('it should return not found when trying to find a product that does not exist', function () {
    $response = $this->getJson(route('api.products.show', -1));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Product] -1');
});
