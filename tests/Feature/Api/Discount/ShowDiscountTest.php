<?php

use App\Models\Campaign;
use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to find a discount', function () {
    $model = new Discount();

    $discounts = Discount::factory()->createMany(2)->pluck('id')->toArray();

    $discount = Discount::factory()->create();

    $response = $this->getJson(route('api.discounts.show', $discount->id));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($model->getFillable());

    $discountId = $response->json()['id'];
    $foundDiscountKey = array_search($discountId, $discounts);

    expect($discountId)
        ->toBe($discount->id)
        ->and($foundDiscountKey)
        ->toBeFalse();
});

test('it should return not found when trying to find a discount that does not exist', function () {
    $response = $this->getJson(route('api.discounts.show', -1));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Discount] -1');
});

test('it should return a discount with its active campaigns', function () {
    $model = new Discount();
    $campaignModel = new Campaign();

    $discount = Discount::factory()->create();
    $campaign = Campaign::factory()->create();
    $campaign->discounts()->attach([$discount->id], ['is_active' => true]);

    $response = $this->getJson(route('api.discounts.show', [
        'discount' => $discount->id,
        'includes' => 'activeCampaigns',
    ]));

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            ...$model->getFillable(),
            'active_campaigns' => [
                '*' => $campaignModel->getFillable(),
            ],
        ]);

    expect($response->json()['active_campaigns'][0]['is_active'])->toBeTrue()
        ->and(count($response->json()['active_campaigns']))->toBe(1);
});

test('it should return a discount with all its campaigns', function () {
    $model = new Discount();
    $campaignModel = new Campaign();

    $discount = Discount::factory()->create();
    $campaign = Campaign::factory()->create();
    $anotherCampaign = Campaign::factory()->create();
    $campaign->discounts()->attach([$discount->id], ['is_active' => true]);
    $discount->campaigns()->attach([$anotherCampaign->id], ['is_active' => false]);

    $response = $this->getJson(route('api.discounts.show', [
        'discount' => $discount->id,
        'includes' => 'campaigns',
    ]));

    $response->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            ...$model->getFillable(),
            'campaigns' => [
                '*' => $campaignModel->getFillable(),
            ],
        ]);

    expect($response->json()['campaigns'][0]['pivot']['is_active'])->toBe(1)
        ->and(count($response->json()['campaigns']))->toBe(2)
        ->and($response->json()['campaigns'][1]['pivot']['is_active'])->toBe(0);
});
