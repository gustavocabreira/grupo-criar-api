<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CampaignProductController extends Controller
{
    public function postAssignProduct(Campaign $campaign, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'products' => ['required', 'array'],
            'products.*' => ['required', 'integer', 'exists:products,id'],
        ]);

        $campaign->products()->attach($validated['products']);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function postRemoveProduct(Campaign $campaign, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'products' => ['required', 'array'],
            'products.*' => ['required', 'integer', 'exists:products,id'],
        ]);

        $campaign->products()->detach($validated['products']);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
