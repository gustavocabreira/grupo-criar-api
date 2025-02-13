<?php

namespace App\Actions\Campaign;

use App\Http\Requests\Campaign\ShowCampaignRequest;
use App\Models\Campaign;

class ShowCampaignAction
{
    public function handle(Campaign $campaign, ShowCampaignRequest $request): Campaign
    {
        $includes = $request->input('includes', []);

        if (!empty($includes)) {
            $campaign->load($includes);
        }

        return $campaign;
    }
}
