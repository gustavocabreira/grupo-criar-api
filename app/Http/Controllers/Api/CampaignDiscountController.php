<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\Discount\AssignDiscountRequest;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CampaignDiscountController extends Controller
{
    public function postAssignDiscounts(Campaign $campaign, AssignDiscountRequest $request): JsonResponse
    {
        $request->validated();

        $discounts = array_unique($request->input('discounts'));

        $campaign->discounts()->attach($discounts, ['is_active' => true]);
        $campaign->load('discounts');

        return response()->json($campaign, Response::HTTP_CREATED);
    }
}
