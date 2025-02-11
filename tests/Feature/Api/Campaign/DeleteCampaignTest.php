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
