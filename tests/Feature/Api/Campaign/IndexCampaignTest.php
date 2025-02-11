<?php

use App\Models\Campaign;
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
