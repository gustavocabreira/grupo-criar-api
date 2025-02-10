<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class StateController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'acronym' => ['required', 'string', 'size:2', 'unique:states,acronym'],
        ]);

        $state = State::query()->create($validated);

        return response()->json($state, Response::HTTP_CREATED);
    }
}
