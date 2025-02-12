<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\Discount\AssignDiscountRequest;
use App\Models\Campaign;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class CampaignDiscountController extends Controller
{
    public function postAssignDiscounts(Campaign $campaign, AssignDiscountRequest $request): JsonResponse
    {
        $request->validated();

        $discountId = $request->input('discount_id');

        DB::transaction(function () use ($campaign, $discountId) {
            DB::table('campaign_discount_pivot')
                ->where('campaign_id', $campaign->id)
                ->where('discount_id', '!=', $discountId)
                ->update(['is_active' => false]);

            $campaign->discounts()->attach([$discountId], ['is_active' => true]);
            $campaign->load('discounts');
        });

        return response()->json($campaign, Response::HTTP_CREATED);
    }
}
