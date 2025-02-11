<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\City\CreateCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CityController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'perPage' => ['sometimes', 'integer', 'min:1'],
        ]);

        $cities = City::query()->with('state')->paginate($request->input('perPage') ?? 10);

        return response()->json($cities, Response::HTTP_OK);
    }

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

    public function update(City $city, UpdateCityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $city->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(City $city): JsonResponse
    {
        $city->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function setActiveStatus(City $city, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $city->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
