<?php

namespace App\Http\Controllers\Api;

use App\Actions\State\CreateStateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\State\CreateStateRequest;
use App\Http\Requests\State\UpdateStateRequest;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $states = State::query()->paginate($request->input('perPage') ?? 10);

        return response()->json($states, Response::HTTP_OK);
    }

    public function store(CreateStateRequest $request, CreateStateAction $action): JsonResponse
    {
        $state = $action->execute($request->validated());

        return response()->json($state, Response::HTTP_CREATED);
    }

    public function show(State $state): JsonResponse
    {
        return response()->json($state, Response::HTTP_OK);
    }

    public function update(State $state, UpdateStateRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $state->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function destroy(State $state): JsonResponse
    {
        $state->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function setActiveStatus(State $state, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $state->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
