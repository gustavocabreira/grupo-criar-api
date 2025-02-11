<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Discount\CreateDiscountRequest;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class DiscountController extends Controller
{
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
}
