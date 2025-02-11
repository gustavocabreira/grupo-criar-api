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
        ['name' => ''], ['name' => ['The name field is required.']],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => ['The name field must not be greater than 255 characters.']],
    ],
    'description with more than 255 characters' => [
        ['description' => Str::repeat('*', 256)], ['description' => ['The description field must not be greater than 255 characters.']],
    ],
    'negative value' => [
        ['value' => '-1'], ['value' => ['The value field must be at least 0.']],
    ],
    'negative percentage' => [
        ['percentage' => '-1'], ['percentage' => ['The percentage field must be at least 0.']],
    ],
    'percentage greater than 100' => [
        ['percentage' => '101'], ['percentage' => ['The percentage field must not be greater than 100.']],
    ],
    'both value and percentage missing' => [
        [], ['value' => ['The value field is required when percentage is not present.'], 'percentage' => ['The percentage field is required when value is not present.']],
    ],
]);

test('it should return unprocessable entity when trying to create a new discount with an invalid payload', function ($payload, $expectedErrors) {
    $model = new Discount();

    $response = $this->postJson(route('api.discounts.store'), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJsonValidationErrors(array_keys($expectedErrors));

    foreach ($expectedErrors as $key => $errorMessages) {
        $response->assertJsonPath("errors.$key", $errorMessages);
    }

    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');

dataset('providing_value_or_percentage', [
    'value' => [
        ['name' => 'value', 'description' => null, 'value' => '100', 'percentage' => 0],
    ],
    'percentage' => [
        ['name' => 'percentage', 'description' => null, 'value' => 0, 'percentage' => '100'],
    ],
]);

test('it should be able to create a new discount when providing value or percentage', function ($payload) {
    $model = new Discount();

    $response = $this->postJson(route('api.discounts.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
})->with('providing_value_or_percentage');


dataset('providing_null_value_or_percentage', [
    'value' => [
        ['name' => 'value', 'description' => null, 'value' => '100', 'percentage' => null],
    ],
    'percentage' => [
        ['name' => 'percentage', 'description' => null, 'value' => null, 'percentage' => '100'],
    ],
]);

test('it should return 0.00 when providing null for value or percentage', function ($payload) {
    $model = new Discount();

    $response = $this->postJson(route('api.discounts.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($model->getFillable());

    $index = $payload['name'];

    $this->assertDatabaseHas($model->getTable(), [
        $index => $payload[$index],
        'description' => $payload['description'],
        'name' => $payload['name'],
        'is_active' => true,
    ]);

    $this->assertDatabaseCount($model->getTable(), 1);
})->with('providing_null_value_or_percentage');
