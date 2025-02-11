<?php

use App\Models\Campaign;
use Illuminate\Http\Response;

test('it should be able to delete a campaign', function () {
    $model = new Campaign();
    $campaign = Campaign::factory()->create();

    $response = $this->deleteJson(route('api.campaigns.destroy', $campaign->id));

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseMissing($model->getTable(), $campaign->toArray());
});

test('it should return not found when trying to delete a city that does not exists', function () {
    $model = new Campaign();

    $campaign = Campaign::factory()->create();

    $response = $this->deleteJson(route('api.campaigns.destroy', ['campaign' => -1]));

    $response
        ->assertStatus(Response::HTTP_NOT_FOUND)
        ->assertJsonPath('message', 'No query results for model [App\Models\Campaign] -1');

    $this->assertDatabaseHas($model->getTable(), $campaign->toArray());
});
