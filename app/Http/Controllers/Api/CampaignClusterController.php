<?php

namespace App\Http\Controllers\Api;

use App\Actions\Campaign\Cluster\AssignClustersAction;
use App\Actions\Campaign\Cluster\SyncClustersAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\Cluster\AssignClustersRequest;
use App\Models\Campaign;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

#[Group(weight: 5)]
class CampaignClusterController extends Controller
{
    /**
     * Assign clusters to a campaign.
     */
    public function postAssignClusters(Campaign $campaign, AssignClustersRequest $request, AssignClustersAction $action): JsonResponse
    {
        $campaign = $action->handle($campaign, $request);
        return response()->json($campaign, Response::HTTP_CREATED);
    }

    /**
     * Sync clusters to a campaign.
     */
    public function postSyncClusters(Campaign $campaign, AssignClustersRequest $request, SyncClustersAction $action): JsonResponse
    {
        $campaign = $action->handle($campaign, $request);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove clusters from a campaign.
     */
    public function postRemoveClusters(Campaign $campaign, AssignClustersRequest $request): JsonResponse
    {
        $request->validated();

        $campaign->clusters()->whereIn('cluster_id', array_unique($request->input('clusters')))->update(['cluster_campaign_pivot.is_active' => false]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
