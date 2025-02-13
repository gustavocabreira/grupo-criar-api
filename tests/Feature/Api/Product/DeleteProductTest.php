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
