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

test('it should return not found when trying to remove a discount from a campaign that does not exists', function () {
    $campaign = Campaign::factory()->create();
    $discount = Discount::factory()->create();

    $payload = [
        'discount_id' => $discount->id,
    ];

    $response = $this->postJson(route('api.campaigns.remove-discounts', ['campaign' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Campaign] -1');

    $this->assertDatabaseMissing('campaign_discount_pivot', [
        'campaign_id' => $campaign->id,
        'discount_id' => $discount->id,
        'is_active' => true,
    ]);
});
