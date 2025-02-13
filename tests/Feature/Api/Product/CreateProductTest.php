<?php

use App\Models\Attachment;
use App\Models\Product;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to create a product', function () {
    $model = new Product();
    Attachment::factory()->count(3)->create();

    $payload = Product::factory()->make()->toArray();

    $response = $this->postJson(route('api.products.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_CREATED)
        ->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'empty description' => [
        ['description' => ''], ['description' => 'The description field is required.'],
    ],
    'empty price' => [
        ['price' => ''], ['price' => 'The price field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'description with more than 255 characters' => [
        ['description' => Str::repeat('*', 256)], ['description' => 'The description field must not be greater than 255 characters.'],
    ],
    'price with less than or equal to 0' => [
        ['price' => -1], ['price' => 'The price field must be at least 1.'],
    ],
]);

test('it should return unprocessable entity when trying to create a product with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);

    $model = new Product();

    $response = $this->postJson(route('api.products.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');

test('it should be able to attach an image to a product', function () {
    $attachments = Attachment::factory()->count(3)->create();
    $product = Product::factory()->make()->toArray();

    $payload = [
        ...$product,
        'attachments' => $attachments->pluck('id')->toArray(),
    ];

    $response = $this->postJson(route('api.products.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    $attachments->each(function ($attachment) use ($response) {
        $this->assertDatabaseHas('product_attachment_pivot', [
            'product_id' => $response['id'],
            'attachment_id' => $attachment->id,
        ]);
    });
});
