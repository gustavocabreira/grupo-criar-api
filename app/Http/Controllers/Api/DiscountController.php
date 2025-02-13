<?php

namespace App\Http\Controllers\Api;

use App\Actions\Discount\IndexDiscountAction;
use App\Actions\Discount\ShowDiscountAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\CreateDiscountRequest;
use App\Http\Requests\Discount\IndexDiscountRequest;
use App\Http\Requests\Discount\SetActiveStatusDiscountRequest;
use App\Http\Requests\Discount\ShowDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DiscountController extends Controller
{
    public function index(IndexDiscountRequest $request, IndexDiscountAction $action): JsonResponse
    {
        $discounts = $action->handle($request);
        return response()->json($discounts, Response::HTTP_OK);
    }

    public function store(CreateDiscountRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $discount = Discount::query()->create($payload);

        return response()->json($discount, Response::HTTP_CREATED);
    }

    public function show(Discount $discount, ShowDiscountRequest $request, ShowDiscountAction $action): JsonResponse
    {
        $discount = $action->handle($discount, $request);
        return response()->json($discount, Response::HTTP_OK);
    }

    public function update(Discount $discount, UpdateDiscountRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $discount->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(Discount $discount): JsonResponse
    {
        $discount->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function setActiveStatus(Discount $discount, SetActiveStatusDiscountRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $discount->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
