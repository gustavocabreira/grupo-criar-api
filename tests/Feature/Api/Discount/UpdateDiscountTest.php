<?php

use App\Models\Discount;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to update a discount', function () {
    $model = new Discount();
    $discount = Discount::factory()->create();
    $payload = Discount::factory()->make()->toArray();

    $response = $this->putJson(route('api.discounts.update', ['discount' => $discount->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $discount->id, ...$payload]);
});

test('it should return not found when trying to update a discount that does not exist', function () {
    $payload = Discount::factory()->make()->toArray();

    $response = $this->putJson(route('api.discounts.update', ['discount' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Discount] -1');
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

test('it should return unprocessable entity when trying to update a discount with an invalid payload', function ($payload, $expectedErrors) {
    $model = new Discount();

    $discount = Discount::factory()->create();

    $response = $this->putJson(route('api.discounts.update', ['discount' => $discount->id]), $payload);

    $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    $response->assertJsonValidationErrors(array_keys($expectedErrors));

    foreach ($expectedErrors as $key => $errorMessages) {
        $response->assertJsonPath("errors.$key", $errorMessages);
    }

    if (count($payload) > 0) {
        $this->assertDatabaseMissing($model->getTable(), $payload);
    }

    $this->assertDatabaseCount($model->getTable(), 1);
})->with('invalid_payload');

dataset('providing_value_or_percentage', [
    'value' => [
        ['name' => 'value', 'description' => null, 'value' => '100', 'percentage' => 0],
    ],
    'percentage' => [
        ['name' => 'percentage', 'description' => null, 'value' => 0, 'percentage' => '100'],
    ],
]);

test('it should be able to update a discount when providing value or percentage', function ($payload) {
    $model = new Discount();

    $discount = Discount::factory()->create();

    $response = $this->putJson(route('api.discounts.update', ['discount' => $discount->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $discount->id,
        'name' => $payload['name'],
        'description' => $payload['description'],
        'value' => $payload['value'],
        'percentage' => $payload['percentage'],
        'is_active' => true,
    ]);
})->with('providing_value_or_percentage');
