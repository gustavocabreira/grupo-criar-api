<?php

use App\Models\Campaign;
use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to make a campaign active', function () {
    $model = new Campaign();
    $campaign = Campaign::factory()->create(['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.campaigns.set-active-status', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $campaign->id,
        ...$payload,
    ]);
});

test('it should return not found when trying to set status of a campaign that does not exist', function () {
    $payload = [
        'is_active' => false,
    ];

    $response = $this->putJson(route('api.campaigns.update', ['campaign' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Campaign] -1');
});

test('it should set discounts relation inactive when campaign is inactive', function () {
    $model = new Campaign();
    $campaign = Campaign::factory()->create(['is_active' => true]);
    $discount = Discount::factory()->create(['is_active' => true]);

    $campaign->discounts()->attach([$discount->id], ['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.campaigns.set-active-status', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $campaign->id,
        ...$payload,
    ]);

    $this->assertDatabaseHas('campaign_discount_pivot', [
        'campaign_id' => $campaign->id,
        'discount_id' => $discount->id,
        'is_active' => false,
    ]);
});
