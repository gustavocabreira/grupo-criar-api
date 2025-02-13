<?php

namespace App\Actions\Product;

use App\Http\Requests\Product\IndexProductRequest;
use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexProductAction
{
    public function handle(IndexProductRequest $request): LengthAwarePaginator
    {
        $products = Product::query();

        if ($request->has('includes')) {
            $products->with($request->input('includes'));
        }

        if ($request->has('name')) {
            $products->filterByName($request->input('name'));
        }

        return $products->paginate($request->input('perPage') ?? 10);
    }
}
