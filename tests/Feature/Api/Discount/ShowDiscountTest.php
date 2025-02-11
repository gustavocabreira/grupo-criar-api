<?php

use App\Models\Discount;
use Illuminate\Http\Response;

test('it should be able to find a discount', function () {
    $model = new Discount();

    $discounts = Discount::factory()->createMany(2)->pluck('id')->toArray();

    $discount = Discount::factory()->create();

    $response = $this->getJson(route('api.discounts.show', $discount->id));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($model->getFillable());

    $discountId = $response->json()['id'];
    $foundDiscountKey = array_search($discountId, $discounts);

    expect($discountId)
        ->toBe($discount->id)
        ->and($foundDiscountKey)
        ->toBeFalse();
});

test('it should return not found when trying to find a discount that does not exist', function () {
    $response = $this->getJson(route('api.discounts.show', -1));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Discount] -1');
});
