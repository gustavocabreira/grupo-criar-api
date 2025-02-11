<?php

use App\Models\Discount;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to create a new discount', function () {
    $model = new Discount();

    $payload = Discount::factory()->make()->toArray();

    $response = $this->postJson(route('api.discounts.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'empty description' => [
        ['description' => ''], ['description' => 'The description field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'description with more than 255 characters' => [
        ['description' => Str::repeat('*', 256)], ['description' => 'The description field must not be greater than 255 characters.'],
    ],
    'empty value' => [
        ['value' => ''], ['value' => 'The value field is required.'],
    ],
    'empty percentage' => [
        ['percentage' => ''], ['percentage' => 'The percentage field is required.'],
    ],
    'negative value' => [
        ['value' => '-1'], ['value' => 'The value field must be at least 0.'],
    ],
    'negative percentage' => [
        ['percentage' => '-1'], ['percentage' => 'The percentage field must be at least 0.'],
    ],
    'percentage greater than 100' => [
        ['percentage' => '101'], ['percentage' => 'The percentage field must not be greater than 100.'],
    ],
]);

test('it should return unprocessable entity when trying to create a new discount with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new Discount();

    $response = $this->postJson(route('api.discounts.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');
