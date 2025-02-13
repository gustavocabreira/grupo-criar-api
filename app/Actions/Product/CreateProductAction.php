<?php

namespace App\Actions\Product;

use App\Http\Requests\Product\CreateProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class CreateProductAction
{
    public function handle(CreateProductRequest $request): Product
    {
        $product = null;

        DB::transaction(function () use ($request, &$product) {
            $product = Product::query()->create($request->validated());

            if ($request->has('attachments')) {
                $product->attachments()->sync(array_unique($request->get('attachments')));
            }
        });

        return $product;
    }
}
