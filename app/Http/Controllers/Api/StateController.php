<?php

namespace App\Http\Controllers\Api;

use App\Actions\State\CreateStateAction;
use App\Actions\State\IndexStateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\State\CreateStateRequest;
use App\Http\Requests\State\IndexStateRequest;
use App\Http\Requests\State\SetActiveStatusStateRequest;
use App\Http\Requests\State\UpdateStateRequest;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StateController extends Controller
{
    /**
     * List states.
     */
    public function index(IndexStateRequest $request, IndexStateAction $action): JsonResponse
    {
        $states = $action->handle($request);
        return response()->json($states, Response::HTTP_OK);
    }

    /**
     * Create a new state.
     */
    public function store(CreateStateRequest $request, CreateStateAction $action): JsonResponse
    {
        $state = $action->handle($request->validated());
        return response()->json($state, Response::HTTP_CREATED);
    }

    /**
     * Show a state.
     */
    public function show(State $state): JsonResponse
    {
        return response()->json($state, Response::HTTP_OK);
    }

    /**
     * Update a state.
     */
    public function update(State $state, UpdateStateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $state->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete a state.
     */
    public function destroy(State $state): JsonResponse
    {
        $state->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Set the active status of a state.
     */
    public function setActiveStatus(State $state, SetActiveStatusStateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $state->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
