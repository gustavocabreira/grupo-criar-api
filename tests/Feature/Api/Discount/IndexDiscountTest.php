<?php

use App\Models\Campaign;
use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to index discounts', function () {
    $model = new Discount();

    Discount::factory()->count(5)->create();

    $response = $this->getJson(route('api.discounts.index'));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => $model->getFillable(),
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);

    expect($response->json()['data'])->toHaveCount(5)
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to see all discounts campaigns', function () {
    $model = new Discount();
    $campaignModel = new Campaign();

    $discount = Discount::factory()->create();
    $campaign = Campaign::factory()->create();
    $campaign->discounts()->attach([$discount->id], ['is_active' => true]);

    $response = $this->getJson(route('api.discounts.index', [
        'includes' => 'campaigns',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'campaigns' => [
                        '*' => $campaignModel->getFillable(),
                    ],
                ],
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);
});

test('it should be able to see all active discounts campaigns', function () {
    $model = new Discount();
    $campaignModel = new Campaign();

    $discount = Discount::factory()->create();
    $anotherDiscount = Discount::factory()->create();
    $campaign = Campaign::factory()->create();
    $campaign->discounts()->attach([$discount->id], ['is_active' => true]);
    $campaign->discounts()->attach([$anotherDiscount->id], ['is_active' => false]);

    $response = $this->getJson(route('api.discounts.index', [
        'includes' => 'activeCampaigns',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'active_campaigns' => [
                        '*' => $campaignModel->getFillable(),
                    ],
                ],
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);

    expect($response->json()['data'][0]['active_campaigns'][0]['is_active'])->toBeTrue()
        ->and(count($response->json()['data'][0]['active_campaigns']))->toBe(1);
});

test('it should be able to set how many discounts will be returned per page', function () {
    Discount::factory()->count(5)->create();

    $payload = [
        'perPage' => 2,
    ];

    $response = $this->getJson(route('api.discounts.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount($payload['perPage'])
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to changes pages', function () {
    Discount::factory()->count(5)->create();

    $payload = [
        'perPage' => 2,
        'page' => 2,
    ];

    $response = $this->getJson(route('api.discounts.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount(2)
        ->and($response->json()['current_page'])->toBe($payload['page'])
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to filter discounts by name', function () {
    $model = new Discount();

    Discount::factory()->create(['name' => 'discount']);
    Discount::factory()->count(3)->create();

    $response = $this->getJson(route('api.discounts.index', [
        'name' => 'discount',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => $model->getFillable(),
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ]);

    expect($response->json()['data'])->toHaveCount(1)
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['total'])->toBe(1);
});
