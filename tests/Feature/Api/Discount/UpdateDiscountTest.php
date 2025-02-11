<?php

use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to update a discount', function () {
    $model = new Discount();
    $discount = Discount::factory()->create();
    $payload = Discount::factory()->make()->toArray();

    $response = $this->putJson(route('api.discounts.update', ['discount' => $discount->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $discount->id, ...$payload]);
});
