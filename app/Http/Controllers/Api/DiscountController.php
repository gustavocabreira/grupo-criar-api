<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiscountController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'value' => ['nullable', 'numeric', 'min:0', 'required_without:percentage'],
            'percentage' => ['nullable', 'numeric', 'min:0', 'max:100', 'required_without:value'],
        ]);

        $discount = Discount::query()->create($payload);

        return response()->json($discount, Response::HTTP_CREATED);
    }
}
