<?php

use App\Models\City;
use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to list all states paginated', function () {
    $model = new State();

    State::factory()->createMany(5);

    $response = $this->getJson(route('api.states.index'));

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

test('it should be able to see all cities of a state', function () {
    $model = new State();
    $cityModel = new City();

    City::factory()->count(5)->create();

    $response = $this->getJson(route('api.states.index', [
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

    expect($response->json()['data'])->toHaveCount(5)
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to see active cities of a state', function () {
    $model = new State();
    $cityModel = new City();

    City::factory()->count(5)->create();

    $response = $this->getJson(route('api.states.index', [
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
        ->and($response->json()['data'])->toHaveCount(5)
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to set how many states will be returned per page', function () {
    State::factory()->createMany(5);

    $payload = [
        'perPage' => 2,
    ];

    $response = $this->getJson(route('api.states.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount($payload['perPage'])
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to changes pages', function () {
    State::factory()->createMany(5);

    $payload = [
        'perPage' => 2,
        'page' => 2,
    ];

    $response = $this->getJson(route('api.states.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount(2)
        ->and($response->json()['current_page'])->toBe($payload['page'])
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});
