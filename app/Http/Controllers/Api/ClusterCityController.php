<?php

namespace App\Http\Controllers\Api;

use App\Actions\Cluster\City\AssignCitiesAction;
use App\Actions\Cluster\City\SyncCitiesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cluster\City\AssignCityRequest;
use App\Http\Requests\Cluster\City\RemoveCityRequest;
use App\Models\Cluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ClusterCityController extends Controller
{
    public function postAssignCities(Cluster $cluster, AssignCityRequest $request, AssignCitiesAction $action): JsonResponse
    {
        $cluster = $action->handle($cluster, $request);
        return response()->json($cluster, Response::HTTP_CREATED);
    }

    public function postSyncCities(Cluster $cluster, AssignCityRequest $request, SyncCitiesAction $action): JsonResponse
    {
        $cluster = $action->handle($cluster, $request);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function postRemoveCities(Cluster $cluster, RemoveCityRequest $request): JsonResponse
    {
        $request->validated();

        $cluster->cities()->whereIn('city_id', array_unique($request->input('cities')))->update(['cluster_city_pivot.is_active' => false]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
