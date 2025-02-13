<?php

namespace App\Actions\Discount;

use App\Http\Requests\Discount\ShowDiscountRequest;
use App\Models\Discount;

class ShowDiscountAction
{
    public function handle(Discount $discount, ShowDiscountRequest $request): Discount
    {
        $includes = $request->input('includes', []);

        if (!empty($includes)) {
            $discount->load($includes);
        }

        return $discount;
    }
}
