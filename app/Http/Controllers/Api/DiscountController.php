<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\CreateDiscountRequest;
use App\Http\Requests\Discount\UpdateDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiscountController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'perPage' => ['sometimes', 'integer', 'min:1'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $discounts = Discount::query()->paginate($request->input('perPage') ?? 10);

        return response()->json($discounts, Response::HTTP_OK);
    }

    public function store(CreateDiscountRequest $request): JsonResponse
    {
        $payload = $request->validated();

        $discount = Discount::query()->create($payload);

        return response()->json($discount, Response::HTTP_CREATED);
    }

    public function show(Discount $discount): JsonResponse
    {
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

    public function setActiveStatus(Discount $discount, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $discount->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
