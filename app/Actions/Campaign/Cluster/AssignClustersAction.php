<?php

namespace App\Actions\Campaign\Cluster;

use App\Http\Requests\Campaign\Cluster\AssignClustersRequest;
use App\Models\Campaign;
use Illuminate\Support\Facades\DB;

class AssignClustersAction
{
    public function handle(Campaign $campaign, AssignClustersRequest $request): Campaign
    {
        $clusters = array_unique($request->input('clusters'));

        DB::transaction(function () use ($campaign, $clusters) {
            DB::table('cluster_campaign_pivot')
                ->whereIn('cluster_id', $clusters)
                ->where('campaign_id', '!=', $campaign->id)
                ->update(['is_active' => false]);

            $campaign->clusters()->attach($clusters, ['is_active' => true]);
            $campaign->load('clusters');
        });

        return $campaign;
    }
}
