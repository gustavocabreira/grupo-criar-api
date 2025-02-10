<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CityController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
        ]);

        $city = City::query()->create($validated);

        return response()->json($city, Response::HTTP_CREATED);
    }
}
