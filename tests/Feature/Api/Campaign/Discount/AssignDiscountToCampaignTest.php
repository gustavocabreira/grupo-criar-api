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

test('it should be able to assign multiple discounts to a campaign', function () {
    $campaign = Campaign::factory()->create();
    $discount = Discount::factory()->count(2)->create();

    $payload = [
        'discounts' => $discount->pluck('id')->toArray(),
    ];

    $response = $this->postJson(route('api.campaigns.assign-discounts', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_CREATED);

    foreach ($discount as $discount) {
        $this->assertDatabaseHas('campaign_discount_pivot', [
            'campaign_id' => $campaign->id,
            'discount_id' => $discount->id,
        ]);
    }

    $this->assertDatabaseCount('campaign_discount_pivot', 2);
});

dataset('invalid_payload', [
    'empty discounts' => [
        ['discounts' => []], ['discounts' => 'The discounts field is required.'],
    ],
    'discount that does not exist' => [
        ['discounts' => [-1]], ['discounts.0' => 'The selected discounts.0 is invalid.'],
    ],
]);

test('it should return unprocessable entity when trying to assign a new discount to a campaign with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);

    $campaign = Campaign::factory()->create();

    $response = $this->postJson(route('api.campaigns.assign-discounts', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    $response->assertJsonValidationErrors($key);

    $response->assertJsonFragment([
        'errors' => [
            $key[0] => [$expectedErrors[$key[0]]],
        ],
    ]);

    if (! empty($payload['discounts'])) {
        $this->assertDatabaseMissing('campaign_discount_pivot', [
            'campaign_id' => $campaign->id,
            'discount_id' => $payload['discounts'][0],
        ]);
    }

    $this->assertDatabaseCount('campaign_discount_pivot', 0);
})->with('invalid_payload');
