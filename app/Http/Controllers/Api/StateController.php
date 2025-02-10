<?php

namespace App\Http\Controllers\Api;

use App\Actions\State\CreateStateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\State\CreateRequest;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StateController extends Controller
{
    public function store(CreateRequest $request, CreateStateAction $action): JsonResponse
    {
        $state = $action->execute($request->validated());

        return response()->json($state, Response::HTTP_CREATED);
    }

    public function show(State $state): JsonResponse
    {
        return response()->json($state, Response::HTTP_OK);
    }

    public function update(State $state, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'acronym' => ['required', 'string', 'size:2', 'unique:states,acronym,'.$state->id],
        ]);

        $state->update($validated);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
