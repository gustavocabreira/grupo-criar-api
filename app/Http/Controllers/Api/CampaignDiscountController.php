<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CampaignDiscountController extends Controller
{
    public function postAssignDiscounts(Campaign $campaign, Request $request): JsonResponse
    {
        $request->validate([
            'discounts' => ['required', 'array'],
            'discounts.*' => ['exists:discounts,id'],
        ]);

        $discounts = array_unique($request->input('discounts'));

        $campaign->discounts()->attach($discounts, ['is_active' => true]);
        $campaign->load('discounts');

        return response()->json($campaign, Response::HTTP_CREATED);
    }
}
