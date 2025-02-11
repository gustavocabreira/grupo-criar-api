<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cluster\City\AssignCityRequest;
use App\Models\Cluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClusterCityController extends Controller
{
    public function store(Cluster $cluster, AssignCityRequest $request): JsonResponse
    {
        $request->validated();

        DB::transaction(function () use ($cluster, $request) {
            DB::table('cluster_city_pivot')
                ->whereIn('city_id', $request->input('cities'))
                ->where('cluster_id', '!=', $cluster->id)
                ->update(['is_active' => false]);

            $cluster->cities()->attach(array_unique($request->input('cities')), ['is_active' => true]);
            $cluster->load('cities');
        });

        return response()->json($cluster, Response::HTTP_CREATED);
    }

    public function destroy(Cluster $cluster, Request $request): JsonResponse
    {
        $request->validate([
            'cities' => ['required', 'array'],
            'cities.*' => ['exists:cities,id'],
        ]);

        $cluster->cities()->whereIn('city_id', array_unique($request->input('cities')))->update(['cluster_city_pivot.is_active' => false]);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
