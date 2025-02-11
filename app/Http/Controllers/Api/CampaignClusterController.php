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
}
