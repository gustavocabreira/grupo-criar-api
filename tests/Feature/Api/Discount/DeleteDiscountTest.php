<?php

use App\Models\Discount;

test('it should be able to delete a discount', function () {
    $model = new Discount();
    $discount = Discount::factory()->create();

    $response = $this->deleteJson(route('api.discounts.destroy', ['discount' => $discount->id]));

    $response->assertStatus(204);

    $this->assertDatabaseMissing($model->getTable(), $discount->toArray());
});

test('it should return not found when trying to delete a discount that does not exists', function () {
    $model = new Discount();

    $discount = Discount::factory()->create();

    $response = $this->deleteJson(route('api.discounts.destroy', ['discount' => -1]));

    $response
        ->assertStatus(404)
        ->assertJsonPath('message', 'No query results for model [App\Models\Discount] -1');

    $this->assertDatabaseHas($model->getTable(), $discount->toArray());
});
