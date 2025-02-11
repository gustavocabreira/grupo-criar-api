<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cluster\CreateClusterRequest;
use App\Models\Cluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClusterController extends Controller
{
    public function store(CreateClusterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cluster = Cluster::query()->create($validated);

        return response()->json($cluster, Response::HTTP_CREATED);
    }

    public function show(Cluster $cluster): JsonResponse
    {
        return response()->json($cluster, Response::HTTP_OK);
    }

    public function update(Cluster $cluster, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $cluster->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
