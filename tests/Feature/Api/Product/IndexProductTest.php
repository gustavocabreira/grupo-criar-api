<?php

use App\Models\Product;
use App\Models\Attachment;
use Illuminate\Http\Response;

test('it should be able to index products', function () {
    $model = new Product();
    $modelAttachment = new Attachment();

    Product::factory()->count(5)->create();

    $response = $this->getJson(route('api.products.index'));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
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

    expect($response->json()['data'])->toHaveCount(5)
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to see attachments of a product', function () {
    $model = new Product();
    $modelAttachment = new Attachment();

    Product::factory()->count(5)->create();

    $response = $this->getJson(route('api.products.index', [
        'includes' => 'attachments',
    ]));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    ...$model->getFillable(),
                    'attachments' => [
                        '*' => $modelAttachment->getFillable(),
                    ]
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

    expect($response->json()['data'])->toHaveCount(5)
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to set how many products will be returned per page', function () {
    Product::factory()->count(5)->create();

    $payload = [
        'perPage' => 2,
    ];

    $response = $this->getJson(route('api.products.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount($payload['perPage'])
        ->and($response->json()['current_page'])->toBe(1)
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to changes pages', function () {
    Product::factory()->count(5)->create();

    $payload = [
        'perPage' => 2,
        'page' => 2,
    ];

    $response = $this->getJson(route('api.products.index', $payload));

    $response->assertStatus(Response::HTTP_OK);

    expect($response->json()['data'])->toHaveCount(2)
        ->and($response->json()['current_page'])->toBe($payload['page'])
        ->and($response->json()['per_page'])->toBe($payload['perPage'])
        ->and($response->json()['total'])->toBe(5);
});

test('it should be able to filter products by name', function () {
    $model = new Product();

    Product::factory()->create(['name' => 'product']);
    Product::factory()->count(3)->create();

    $response = $this->getJson(route('api.products.index', [
        'name' => 'product',
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
