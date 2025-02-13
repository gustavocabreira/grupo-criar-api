<?php

use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

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

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => ['The name field is required.']],
    ],
    'empty description' => [
        ['description' => ''], ['description' => ['The description field is required.']],
    ],
    'empty price' => [
        ['price' => ''], ['price' => ['The price field is required.']],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => ['The name field must not be greater than 255 characters.']],
    ],
    'description with more than 255 characters' => [
        ['description' => Str::repeat('*', 256)], ['description' => ['The description field must not be greater than 255 characters.']],
    ],
    'price with less than or equal to 0' => [
        ['price' => -1], ['price' => ['The price field must be at least 1.']],
    ],
]);

test('it should return unprocessable entity when trying to update a product with an invalid payload', function ($payload, $expectedErrors) {
    $model = new Product();

    $product = Product::factory()->create();

    $response = $this->putJson(route('api.products.update', ['product' => $product->id]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJsonValidationErrors(array_keys($expectedErrors));

    foreach ($expectedErrors as $key => $errorMessages) {
        $response->assertJsonPath("errors.$key", $errorMessages);
    }

    if (count($payload) > 0) {
        $this->assertDatabaseMissing($model->getTable(), $payload);
    }

    $this->assertDatabaseCount($model->getTable(), 1);
})->with('invalid_payload');
