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
