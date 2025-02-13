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
