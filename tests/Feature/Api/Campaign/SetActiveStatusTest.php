<?php

use App\Models\Campaign;
use Illuminate\Http\Response;

test('it should be able to make a campaign active', function () {
    $model = new Campaign();
    $campaign = Campaign::factory()->create(['is_active' => true]);

    $payload = [
        'is_active' => false,
    ];

    $response = $this->patchJson(route('api.campaigns.set-active-status', ['campaign' => $campaign->id]), $payload);

    $response->assertStatus(Response::HTTP_NO_CONTENT);

    $this->assertDatabaseHas($model->getTable(), [
        'id' => $campaign->id,
        ...$payload,
    ]);
});
