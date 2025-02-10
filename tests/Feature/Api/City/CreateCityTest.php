<?php

use App\Models\City;
use Illuminate\Http\Response;

test('it should be able to create a new city', function() {
    $table = new City();
    $payload = City::factory()->make()->toArray();

    $response = $this->postJson(route('api.cities.store'), $payload);

    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($table->getFillable());

    $this->assertDatabaseHas($table->getTable(), $payload);
    $this->assertDatabaseCount($table->getTable(), 1);
});
