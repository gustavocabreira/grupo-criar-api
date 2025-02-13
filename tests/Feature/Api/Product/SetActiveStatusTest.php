<?php

use App\Models\Product;
use Illuminate\Http\Response;

test('it should ble to make a product active', function () {
    $model = new Product();
    $product = Product::factory()->create(['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.products.set-active-status', ['product' => $product->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $product->id,
        ...$payload,
    ]);
});
