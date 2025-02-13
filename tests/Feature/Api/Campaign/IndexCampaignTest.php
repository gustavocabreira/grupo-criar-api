<?php

use App\Models\Campaign;
use App\Models\Cluster;
use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to list all campaigns paginated', function () {
    $model = new Campaign();

    Campaign::factory()->createMany(5);

    $response = $this->getJson(route('api.campaigns.index'));

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

    expect($response->json()['data'])->toHaveCount(5)
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to set how many campaigns will be returned per page', function () {
    Campaign::factory()->createMany(5);

    $payload = [
        'perPage' => 2,
    ];

    $response = $this->getJson(route('api.campaigns.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount($payload['perPage'])
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to changes pages', function () {
    Campaign::factory()->createMany(5);

    $payload = [
        'perPage' => 2,
        'page' => 2,
    ];

    $response = $this->getJson(route('api.campaigns.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount(2)
        ->and($response->json()['current_page'])->toBe($payload['page'])
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to see all clusters campaigns', function () {
    $model = new Campaign();
    $clusterModel = new Cluster();

    $campaign = Campaign::factory()->create();
    $cluster = Cluster::factory()->create();
    $campaign->clusters()->attach([$cluster->id], ['is_active' => true]);

    $response = $this->getJson(route('api.campaigns.index', [
        'includes' => 'clusters',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'clusters' => [
                        '*' => $clusterModel->getFillable(),
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
            'total',
        ]);
});

test('it should be able to see all active clusters campaigns', function () {
    $model = new Campaign();
    $clusterModel = new Cluster();

    $campaign = Campaign::factory()->create();
    $anotherCampaign = Campaign::factory()->create();
    $cluster = Cluster::factory()->create();
    $campaign->clusters()->attach([$cluster->id], ['is_active' => true]);
    $anotherCampaign->clusters()->attach([$cluster->id], ['is_active' => false]);

    $response = $this->getJson(route('api.campaigns.index', [
        'includes' => 'activeClusters',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'active_clusters' => [
                        '*' => $clusterModel->getFillable(),
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
            'total',
        ]);

    expect($response->json()['data'][0]['active_clusters'][0]['is_active'])->toBeTrue()
        ->and(count($response->json()['data'][0]['active_clusters']))->toBe(1);
});

test('it should be able to see all discounts campaigns', function () {
    $model = new Campaign();
    $discountModel = new Campaign();

    $campaign = Campaign::factory()->create();
    $discount = Discount::factory()->create();
    $campaign->discounts()->attach([$discount->id], ['is_active' => true]);

    $response = $this->getJson(route('api.campaigns.index', [
        'includes' => 'discounts',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'discounts' => [
                        '*' => $discountModel->getFillable(),
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
            'total',
        ]);

    expect($response->json()['data'][0]['discounts'][0]['is_active'])->toBeTrue();
});

test('it should be able to see active discounts of a campaign', function () {
    $model = new Campaign();
    $discountModel = new Campaign();

    $campaign = Campaign::factory()->create();
    $discount = Discount::factory()->count(2)->create();
    $campaign->discounts()->attach([$discount->first()->id], ['is_active' => true]);
    $campaign->discounts()->attach([$discount->last()->id], ['is_active' => false]);

    $response = $this->getJson(route('api.campaigns.index', [
        'includes' => 'activeDiscounts',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'active_discounts' => [
                        '*' => $discountModel->getFillable(),
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
            'total',
        ]);

    expect($response->json()['data'][0]['active_discounts'][0]['is_active'])->toBeTrue()
        ->and(count($response->json()['data'][0]['active_discounts']))->toBe(1);
});
