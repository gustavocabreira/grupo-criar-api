<?php

namespace App\Actions\Campaign\Cluster;

use App\Http\Requests\Campaign\Cluster\AssignClustersRequest;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;

class SyncClustersAction
{
    public function handle(Campaign $campaign, AssignClustersRequest $request): Campaign
    {
        $clusters = array_unique($request->input('clusters'));

        DB::transaction(function () use ($campaign, $clusters) {
            DB::table('cluster_campaign_pivot')
                ->whereIn('cluster_id', $clusters)
                ->where('campaign_id', '!=', $campaign->id)
                ->update(['is_active' => false]);

            $campaign->clusters()->whereNotIn('cluster_id', $clusters)->update(['cluster_campaign_pivot.is_active' => false]);
            $campaign->clusters()->syncWithoutDetaching($clusters);
            $campaign->clusters()->whereIn('cluster_id', $clusters)->update(['cluster_campaign_pivot.is_active' => true]);
        });

        return $campaign;
    }
}
