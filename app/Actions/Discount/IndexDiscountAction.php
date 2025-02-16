<?php

namespace App\Actions\Discount;

use App\Http\Requests\Discount\IndexDiscountRequest;
use App\Models\Discount;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexDiscountAction
{
    public function handle(IndexDiscountRequest $request): LengthAwarePaginator
    {
        $discounts = Discount::query();

        if ($request->has('includes')) {
            $discounts->with($request->input('includes'));
        }

        if ($request->has('name')) {
            $discounts->filterByName($request->input('name'));
        }

        return $discounts->paginate($request->input('perPage') ?? 10);
    }
}
