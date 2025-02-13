<?php

namespace App\Http\Controllers\Api;

use App\Actions\Cluster\City\AssignCitiesAction;
use App\Actions\Cluster\City\SyncCitiesAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cluster\City\AssignCityRequest;
use App\Http\Requests\Cluster\City\RemoveCityRequest;
use App\Models\Cluster;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

#[Group(weight: 4)]
class ClusterCityController extends Controller
{
    /**
     * Assign cities to a cluster.
     */
    public function postAssignCities(Cluster $cluster, AssignCityRequest $request, AssignCitiesAction $action): JsonResponse
    {
        $cluster = $action->handle($cluster, $request);
        return response()->json($cluster, Response::HTTP_CREATED);
    }

    /**
     * Sync cities to a cluster.
     */
    public function postSyncCities(Cluster $cluster, AssignCityRequest $request, SyncCitiesAction $action): JsonResponse
    {
        $cluster = $action->handle($cluster, $request);
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Remove cities from a cluster.
     */
    public function postRemoveCities(Cluster $cluster, RemoveCityRequest $request): JsonResponse
    {
        $request->validated();

        $cluster->cities()->whereIn('city_id', array_unique($request->input('cities')))->update(['cluster_city_pivot.is_active' => false]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
