<?php

use App\Models\Campaign;
use App\Models\Product;
use Illuminate\Http\Response;

test('it should assign products to a campaign', function () {
    $product = Product::factory()->create();
    $campaign = Campaign::factory()->create();

    $payload =  [
        'products' => [$product->id],
    ];

    $response = $this->postJson(route('api.campaigns.assign-product', [
        'campaign' => $campaign->id,
    ]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas('campaign_product_pivot', [
        'campaign_id' => $campaign->id,
        'product_id' => $product->id,
    ]);

    $this->assertDatabaseCount('campaign_product_pivot', 1);
});

dataset('invalid_payload', [
    'empty products' => [
        ['products' => []], ['products' => 'The products field is required.'],
    ],
    'product that does not exist' => [
        ['products' => [-1]], ['products.0' => 'The selected products.0 is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to assign a new product to a campaign with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $product = Product::factory()->create();

    $campaign = Campaign::factory()->create();

    $response = $this->postJson(route('api.campaigns.assign-product', [
        'campaign' => $campaign->id,
    ]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $response->assertJsonValidationErrors($key);

    $response->assertJsonFragment([
        'errors' => [
            $key[0] => [$expectedErrors[$key[0]]],
        ],
    ]);

    $this->assertDatabaseMissing('campaign_product_pivot', [
        'product_id' => $product->id,
    ]);

    $this->assertDatabaseCount('campaign_product_pivot', 0);
})->with('invalid_payload');
