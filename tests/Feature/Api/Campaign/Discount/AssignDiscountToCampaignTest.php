<?php

use App\Models\Campaign;
use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to assign a discount to a campaign', function () {
    $campaign = Campaign::factory()->create();
    $discount = Discount::factory()->create();

    $payload = [
        'discount_id' => $discount->id,
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

dataset('invalid_payload', [
    'empty discount_id' => [
        ['discount_id' => ''], ['discount_id' => ['The discount id field is required.']],
    ],
    'discount that does not exist' => [
        ['discount_id' => -1], ['discount_id' => ['The selected discount id is invalid.']],
    ],
]);

test('it should return unprocessable entity when trying to assign a new discount to a campaign with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    Discount::factory()->create();

    $campaign = Campaign::factory()->create();

    $response = $this->postJson(route('api.campaigns.assign-discounts', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $response->assertJsonValidationErrors($key);

    $response->assertJsonFragment(['errors' => $expectedErrors]);

    if (! empty($payload['discount_id'])) {
        $this->assertDatabaseMissing('campaign_discount_pivot', [
            'campaign_id' => $campaign->id,
            'discount_id' => $payload['discount_id'],
        ]);
    }

    $this->assertDatabaseCount('campaign_discount_pivot', 0);
})->with('invalid_payload');
