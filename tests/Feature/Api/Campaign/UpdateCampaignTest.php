<?php

use App\Models\Campaign;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

test('it should be able to update a campaign', function () {
    $model = new Campaign();
    $campaign = Campaign::factory()->create();
    $payload = Campaign::factory()->make()->toArray();

    $response = $this->putJson(route('api.campaigns.update', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), ['id' => $campaign->id, ...$payload]);
});

test('it should return not found when trying to update a campaign that does not exist', function () {
    $payload = Campaign::factory()->make()->toArray();

    $response = $this->putJson(route('api.campaigns.update', ['campaign' => -1]), $payload);

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Campaign] -1');
});

dataset('invalid_payload', [
    'empty name' => [
        ['name' => ''], ['name' => 'The name field is required.'],
    ],
    'empty description' => [
        ['description' => ''], ['description' => 'The description field is required.'],
    ],
    'name with more than 255 characters' => [
        ['name' => Str::repeat('*', 256)], ['name' => 'The name field must not be greater than 255 characters.'],
    ],
    'description with more than 255 characters' => [
        ['description' => Str::repeat('*', 256)], ['description' => 'The description field must not be greater than 255 characters.'],
    ],
]);

test('it should return unprocessable entity when trying to update a campaign with an invalid payload', function ($payload, $expectedErrors) {
    $key = array_keys($expectedErrors);
    $model = new Campaign();

    $campaign = Campaign::factory()->create();

    $response = $this->putJson(route('api.campaigns.update', ['campaign' => $campaign->id]), $payload);

    $response
        ->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
        ->assertJsonValidationErrors($key);

    $response->assertJsonPath("errors.$key[0].0", $expectedErrors[$key[0]]);

    $this->assertDatabaseMissing($model->getTable(), $payload);
    $this->assertDatabaseCount($model->getTable(), 1);
})->with('invalid_payload');
