<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CampaignClusterController extends Controller
{
    public function store(Campaign $campaign, Request $request): JsonResponse
    {
        $request->validate([
            'clusters' => ['required', 'array'],
            'clusters.*' => ['integer', 'exists:clusters,id'],
        ]);

        $clusters = array_unique($request->input('clusters'));

        DB::transaction(function () use ($campaign, $clusters) {
            DB::table('cluster_campaign_pivot')
                ->whereIn('cluster_id', $clusters)
                ->where('campaign_id', '!=', $campaign->id)
                ->update(['is_active' => false]);

            $campaign->clusters()->attach($clusters, ['is_active' => true]);
            $campaign->load('clusters');
        });

        return response()->json($campaign, Response::HTTP_CREATED);
    }

    public function update(Campaign $campaign, Request $request): JsonResponse
    {
        $request->validate([
            'clusters' => ['required', 'array'],
            'clusters.*' => ['exists:clusters,id'],
        ]);

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

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Campaign $campaign, Request $request): JsonResponse
    {
        $request->validate([
            'clusters' => ['required', 'array'],
            'clusters.*' => ['integer', 'exists:clusters,id'],
        ]);

        $campaign->clusters()->whereIn('cluster_id', array_unique($request->input('clusters')))->update(['cluster_campaign_pivot.is_active' => false]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
