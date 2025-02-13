<?php

namespace App\Http\Controllers\Api;

use App\Actions\Campaign\IndexCampaignAction;
use App\Actions\Campaign\ShowCampaignAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Http\Requests\Campaign\IndexCampaignRequest;
use App\Http\Requests\Campaign\SetActiveStatusCampaignRequest;
use App\Http\Requests\Campaign\ShowCampaignRequest;
use App\Http\Requests\Campaign\UpdateCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CampaignController extends Controller
{
    public function index(IndexCampaignRequest $request, IndexCampaignAction $action): JsonResponse
    {
        $campaigns = $action->handle($request);
        return response()->json($campaigns, Response::HTTP_OK);
    }

    public function store(CreateCampaignRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $campaign = Campaign::query()->create($validated);

        return response()->json($campaign, Response::HTTP_CREATED);
    }

    public function show(Campaign $campaign, ShowCampaignRequest $request, ShowCampaignAction $action): JsonResponse
    {
        $campaign = $action->handle($campaign, $request);
        return response()->json($campaign, Response::HTTP_OK);
    }

    public function update(Campaign $campaign, UpdateCampaignRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $campaign->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Campaign $campaign): JsonResponse
    {
        $campaign->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function setActiveStatus(Campaign $campaign, SetActiveStatusCampaignRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $campaign->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
