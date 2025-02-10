<?php

use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\StateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::name('api.')->group(function () {
    Route::apiResource('states', StateController::class);
    Route::patch('states/{state}/set-active-status', [StateController::class, 'setActiveStatus'])->name('states.set-active-status');

    Route::apiResource('cities', CityController::class);
});
