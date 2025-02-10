<?php


use App\Models\City;
use App\Models\State;
use Illuminate\Http\Response;

test('it should be able to list all cities paginated', function () {
    $model = new City;

    City::factory()->createMany(5);

    $response = $this->getJson(route('api.cities.index'));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [...$model->getFillable(), 'state' => new State()->getFillable()],
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

