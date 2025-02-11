<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CampaignController extends Controller
{
    public function store(CreateCampaignRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $campaign = Campaign::query()->create($validated);

        return response()->json($campaign, Response::HTTP_CREATED);
    }

    public function show(Campaign $campaign): JsonResponse
    {
        return response()->json($campaign, Response::HTTP_OK);
    }
}
