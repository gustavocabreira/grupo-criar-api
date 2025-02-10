<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\City\CreateCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

    public function update(City $city, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'state_id' => ['required', 'integer', 'exists:states,id'],
        ]);

        $city->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
