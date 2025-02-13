<?php

namespace App\Http\Controllers\Api;

use App\Actions\City\IndexCityAction;
use App\Actions\City\ShowCityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\City\CreateCityRequest;
use App\Http\Requests\City\IndexCityRequest;
use App\Http\Requests\City\SetActiveStatusCityRequest;
use App\Http\Requests\City\ShowCityRequest;
use App\Http\Requests\City\UpdateCityRequest;
use App\Models\City;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CityController extends Controller
{
    public function index(IndexCityRequest $request, IndexCityAction $action): JsonResponse
    {
        $cities = $action->handle($request);
        return response()->json($cities, Response::HTTP_OK);
    }

    public function store(CreateCityRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $city = City::query()->create($validated);

        return response()->json($city, Response::HTTP_CREATED);
    }

    public function show(City $city, ShowCityRequest $request, ShowCityAction $action): JsonResponse
    {
        $city = $action->handle($city, $request);
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

    public function setActiveStatus(City $city, SetActiveStatusCityRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $city->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
