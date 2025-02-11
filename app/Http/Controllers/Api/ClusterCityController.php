<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ClusterCityController extends Controller
{
    public function store(Cluster $cluster, Request $request): JsonResponse
    {
        $request->validate([
            'cities' => ['required', 'array'],
            'cities.*' => ['exists:cities,id'],
        ]);

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
}
