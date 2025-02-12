<?php

use App\Models\Campaign;
use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to remove a discount from a campaign', function () {
    $campaign = Campaign::factory()->create();
    $discount = Discount::factory()->create();

    $campaign->discounts()->attach([$discount->id], ['is_active' => true]);

    $payload = [
        'discount_id' => $discount->id,
    ];

    $response = $this->postJson(route('api.campaigns.remove-discounts', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas('campaign_discount_pivot', [
        'campaign_id' => $campaign->id,
        'discount_id' => $discount->id,
        'is_active' => false,
    ]);

    $this->assertDatabaseCount('campaign_discount_pivot', 1);
});
