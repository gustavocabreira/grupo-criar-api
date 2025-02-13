<?php

namespace App\Http\Controllers\Api;

use App\Actions\Product\CreateProductAction;
use App\Actions\Product\IndexProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Product\CreateProductRequest;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\SetActiveStatusProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * List products.
     */
    public function index(IndexProductRequest $request, IndexProductAction $action): JsonResponse
    {
        $products = $action->handle($request);
        return response()->json($products, Response::HTTP_OK);
    }

    /**
     * Create a new product.
     */
    public function store(CreateProductRequest $request, CreateProductAction $action): JsonResponse
    {
        $product = $action->handle($request);
        return response()->json($product, Response::HTTP_CREATED);
    }

    /**
     * Show a product.
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product, Response::HTTP_OK);
    }

    /**
     * Update a product.
     */
    public function update(Product $product, UpdateProductRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $product->update($payload);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the active status of a product.
     */
    public function setActiveStatus(Product $product, SetActiveStatusProductRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $product->update($payload);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
