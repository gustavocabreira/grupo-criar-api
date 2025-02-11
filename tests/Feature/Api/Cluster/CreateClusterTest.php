<?php

use App\Models\Cluster;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to create a new cluster', function () {
    $model = new Cluster();

    $payload = Cluster::factory()->make()->toArray();

    $response = $this->postJson(route('api.clusters.store'), $payload);
    $response->assertStatus(Response::HTTP_CREATED);
    $response->assertJsonStructure($model->getFillable());

    $this->assertDatabaseHas($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
]);

test('it should return unprocessable entity when trying to create a new cluster with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new Cluster();

    $response = $this->postJson(route('api.cities.store'), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 0);
})->with('invalid_payload');
