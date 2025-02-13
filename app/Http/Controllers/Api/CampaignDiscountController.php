<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Campaign\Discount\AssignDiscountRequest;
use App\Http\Requests\Campaign\Discount\RemoveDiscountRequest;
use App\Models\Campaign;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

#[Group(weight: 7)]
class CampaignDiscountController extends Controller
{
    /**
     * Assign discounts to a campaign.
     */
    public function postAssignDiscount(Campaign $campaign, AssignDiscountRequest $request): JsonResponse
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

    /**
     * Remove discounts from a campaign.
     */
    public function postRemoveDiscount(Campaign $campaign, RemoveDiscountRequest $request): JsonResponse
    {
        $request->validated();

        $campaign->discounts()->where('discount_id', $request->input('discount_id'))->update(['campaign_discount_pivot.is_active' => false]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
