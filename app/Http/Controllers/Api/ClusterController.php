<?php

namespace App\Http\Controllers\Api;

use App\Actions\Cluster\IndexClusterAction;
use App\Actions\Cluster\ShowClusterAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Cluster\CreateClusterRequest;
use App\Http\Requests\Cluster\IndexClusterRequest;
use App\Http\Requests\Cluster\SetActiveStatusClusterRequest;
use App\Http\Requests\Cluster\ShowClusterRequest;
use App\Http\Requests\Cluster\UpdateClusterRequest;
use App\Models\Cluster;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ClusterController extends Controller
{
    /**
     * List clusters.
     */
    public function index(IndexClusterRequest $request, IndexClusterAction $action): JsonResponse
    {
        $cluster = $action->handle($request);
        return response()->json($cluster, Response::HTTP_OK);
    }

    /**
     * Create a new cluster.
     */
    public function store(CreateClusterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cluster = Cluster::query()->create($validated);

        return response()->json($cluster, Response::HTTP_CREATED);
    }

    /**
     * Show a cluster.
     */
    public function show(Cluster $cluster, ShowClusterRequest $request, ShowClusterAction $action): JsonResponse
    {
        $cluster = $action->handle($cluster, $request);
        return response()->json($cluster, Response::HTTP_OK);
    }

    /**
     * Update a cluster.
     */
    public function update(Cluster $cluster, UpdateClusterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cluster->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete a cluster.
     */
    public function destroy(Cluster $cluster): JsonResponse
    {
        $cluster->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the active status of a cluster.
     */
    public function setActiveStatus(Cluster $cluster, SetActiveStatusClusterRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $cluster->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
