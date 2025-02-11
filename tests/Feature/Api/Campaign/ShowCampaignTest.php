<?php

use App\Models\Campaign;
use Illuminate\Http\Response;

test('it should be able to find a campaign', function () {
    $model = new Campaign();

    $cities = Campaign::factory()->createMany(2)->pluck('id')->toArray();

    $campaign = Campaign::factory()->create();

    $response = $this->getJson(route('api.campaigns.show', $campaign->id));

    $response
        ->assertStatus(Response::HTTP_OK)
        ->assertJsonStructure($model->getFillable());

    $campaignId = $response->json()['id'];
    $foundStateKey = array_search($campaignId, $cities);

    expect($campaignId)
        ->toBe($campaign->id)
        ->and($foundStateKey)
        ->toBeFalse();
});
