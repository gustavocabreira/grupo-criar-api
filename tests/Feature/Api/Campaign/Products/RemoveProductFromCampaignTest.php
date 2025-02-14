<?php

use App\Models\Campaign;
use App\Models\Product;

test('it should remove products from a campaign', function () {
    $product = Product::factory()->create();
    $campaign = Campaign::factory()->create();

    $payload =  [
        'products' => [$product->id],
    ];

    $response = $this->postJson(route('api.campaigns.remove-product', [
        'campaign' => $campaign->id,
    ]), $payload);

    $response->assertStatus(204);

    $this->assertDatabaseMissing('campaign_product_pivot', [
        'campaign_id' => $campaign->id,
        'product_id' => $product->id,
    ]);

    $this->assertDatabaseCount('campaign_product_pivot', 0);
});
