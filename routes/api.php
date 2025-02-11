<?php

use App\Http\Controllers\Api\CampaignClusterController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ClusterCityController;
use App\Http\Controllers\Api\ClusterController;
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
    Route::patch('cities/{city}/set-active-status', [CityController::class, 'setActiveStatus'])->name('cities.set-active-status');

    Route::apiResource('clusters', ClusterController::class);
    Route::patch('clusters/{cluster}/set-active-status', [ClusterController::class, 'setActiveStatus'])->name('clusters.set-active-status');

    Route::prefix('clusters')
        ->controller(ClusterCityController::class)
        ->group(function () {
            Route::post('{cluster}/cities', 'store')->name('clusters.cities.store');
            Route::put('{cluster}/cities', 'update')->name('clusters.cities.update');
            Route::delete('{cluster}/cities', 'destroy')->name('clusters.cities.destroy');
        });

    Route::apiResource('campaigns', CampaignController::class);
    Route::patch('campaigns/{campaign}/set-active-status', [CampaignController::class, 'setActiveStatus'])->name('campaigns.set-active-status');

    Route::prefix('campaigns')
        ->controller(CampaignClusterController::class)
        ->group(function () {
            Route::post('{campaign}/clusters', 'store')->name('campaigns.clusters.store');
            Route::delete('{campaign}/clusters', 'destroy')->name('campaigns.clusters.destroy');
        });
});
