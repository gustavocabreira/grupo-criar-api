<?php

use App\Models\Discount;

test('it should be able to delete a discount', function () {
    $model = new Discount();
    $discount = Discount::factory()->create();

    $response = $this->deleteJson(route('api.discounts.destroy', ['discount' => $discount->id]));

    $response->assertStatus(204);

    $this->assertDatabaseMissing($model->getTable(), $discount->toArray());
});
