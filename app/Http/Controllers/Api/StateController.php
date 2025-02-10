<?php

namespace App\Http\Controllers\Api;

use App\Actions\State\CreateStateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\State\CreateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class StateController extends Controller
{
    public function store(CreateRequest $request, CreateStateAction $action): JsonResponse
    {
        $state = $action->execute($request->validated());
        return response()->json($state, Response::HTTP_CREATED);
    }
}
