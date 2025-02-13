<?php

use App\Models\City;
use App\Models\Cluster;
use Illuminate\Http\Response;

test('it should be able to list all clusters paginated', function () {
    $model = new Cluster();

    Cluster::factory()->createMany(5);

    $response = $this->getJson(route('api.clusters.index'));

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

test('it should be able to see cities of a cluster', function () {
    $model = new Cluster();
    $cityModel = new Cluster();

    $cluster = Cluster::factory()->create();
    $city = City::factory()->create();
    $cluster->cities()->attach([$city->id], ['is_active' => true]);

    $response = $this->getJson(route('api.clusters.index', [
        'includes' => 'cities',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'cities' => [
                        '*' => $cityModel->getFillable(),
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

    expect($response->json()['data'][0]['cities'][0]['is_active'])->toBeTrue();
});

test('it should be able to see active cities of a cluster', function () {
    $model = new Cluster();
    $cityModel = new Cluster();

    $cluster = Cluster::factory()->create();
    $city = City::factory()->count(2)->create();
    $cluster->cities()->attach([$city->first()->id], ['is_active' => true]);
    $cluster->cities()->attach([$city->last()->id], ['is_active' => false]);

    $response = $this->getJson(route('api.clusters.index', [
        'includes' => 'activeCities',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'active_cities' => [
                        '*' => $cityModel->getFillable(),
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

    expect($response->json()['data'][0]['active_cities'][0]['is_active'])->toBeTrue()
        ->and(count($response->json()['data'][0]['active_cities']))->toBe(1);
});

test('it should be able to set how many clusters will be returned per page', function () {
    Cluster::factory()->createMany(5);

    $payload = [
        'perPage' => 2,
    ];

    $response = $this->getJson(route('api.clusters.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount($payload['perPage'])
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to changes pages', function () {
    Cluster::factory()->createMany(5);

    $payload = [
        'perPage' => 2,
        'page' => 2,
    ];

    $response = $this->getJson(route('api.clusters.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount(2)
        ->and($response->json()['current_page'])->toBe($payload['page'])
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to filter cluster by name', function () {
    $model = new Cluster();

    Cluster::factory()->create(['name' => 'cluster']);
    Cluster::factory()->count(3)->create();

    $response = $this->getJson(route('api.clusters.index', [
        'name' => 'cluster',
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
