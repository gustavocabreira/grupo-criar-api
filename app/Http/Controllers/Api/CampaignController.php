<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\CreateCampaignRequest;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CampaignController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'perPage' => ['sometimes', 'integer', 'min:1'],
        ]);

        $campaigns = Campaign::query()->paginate($request->input('perPage') ?? 10);

        return response()->json($campaigns, Response::HTTP_OK);
    }

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

    public function update(Campaign $campaign, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        $campaign->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Campaign $campaign): JsonResponse
    {
        $campaign->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
