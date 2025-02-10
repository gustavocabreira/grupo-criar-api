<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\City\CreateCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CityController extends Controller
{
    public function store(CreateCityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $city = City::query()->create($validated);

        return response()->json($city, Response::HTTP_CREATED);
    }

    public function show(City $city): JsonResponse
    {
        return response()->json($city, Response::HTTP_OK);
    }
}
