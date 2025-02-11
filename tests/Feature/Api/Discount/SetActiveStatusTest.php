<?php

use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to make a discount active', function () {
    $model = new Discount();
    $discount = Discount::factory()->create(['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.discounts.set-active-status', ['discount' => $discount->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $discount->id,
        ...$payload,
    ]);
});

test('it should return not found when trying to set status of a discount that does not exist', function () {
    $payload = [
        'is_active' => false,
    ];

    $response = $this->putJson(route('api.discounts.update', ['discount' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Discount] -1');
});
