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
    public function index(Request $request): JsonResponse
    {
        $cluster = Cluster::query()->paginate($request->input('perPage') ?? 10);

        return response()->json($cluster, Response::HTTP_OK);
    }

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

    public function destroy(Cluster $cluster): JsonResponse
    {
        $cluster->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function setActiveStatus(Cluster $cluster, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $cluster->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
