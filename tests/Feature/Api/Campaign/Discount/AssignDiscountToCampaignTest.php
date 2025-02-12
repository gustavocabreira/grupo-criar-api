<?php

use App\Models\Campaign;
use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to assign a discount to a campaign', function () {
    $campaign = Campaign::factory()->create();
    $discount = Discount::factory()->create();

    $payload = [
        'discounts' => [$discount->id],
    ];

    $response = $this->postJson(route('api.campaigns.assign-discounts', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    $this->assertDatabaseHas('campaign_discount_pivot', [
        'campaign_id' => $campaign->id,
        'discount_id' => $discount->id,
        'is_active' => true,
    ]);

    $this->assertDatabaseCount('campaign_discount_pivot', 1);
});
