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

test('it should return not found when trying to set status of a product that does not exist', function () {
    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.products.set-active-status', ['product' => -1]), $payload);
    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Product] -1');
});
