<?php

namespace App\Actions\Campaign;

use App\Http\Requests\Campaign\IndexCampaignRequest;
use App\Models\Campaign;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexCampaignAction
{
    public function handle(IndexCampaignRequest $request): LengthAwarePaginator
    {
        $campaigns = Campaign::query();

        if ($request->has('includes')) {
            $campaigns->with($request->input('includes'));
        }

        return $campaigns->paginate($request->input('perPage') ?? 10);
    }
}
