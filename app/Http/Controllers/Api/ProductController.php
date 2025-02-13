<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    public function store(CreateProductRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $product = Product::query()->create($payload);

        return response()->json($product, Response::HTTP_CREATED);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json($product, Response::HTTP_OK);
    }

    public function update(Product $product, UpdateProductRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $product->update($payload);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
