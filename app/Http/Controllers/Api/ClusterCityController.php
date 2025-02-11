<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClusterCityController extends Controller
{
    public function store(Cluster $cluster, Request $request): JsonResponse
    {
        $cluster->cities()->attach($request->input('cities'));
        $cluster->load('cities');

        return response()->json($cluster, Response::HTTP_CREATED);
    }
}
