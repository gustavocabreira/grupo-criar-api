<?php

use App\Http\Controllers\Api\CampaignClusterController;
use App\Http\Controllers\Api\CampaignController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\ClusterCityController;
use App\Http\Controllers\Api\ClusterController;
use App\Http\Controllers\Api\DiscountController;
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
            Route::post('{cluster}/assign-cities', 'postAssignCities')->name('clusters.assign-cities');
            Route::post('{cluster}/sync-cities', 'postSyncCities')->name('clusters.sync-cities');
            Route::post('{cluster}/remove-cities', 'postRemoveCities')->name('clusters.remove-cities');
        });

    Route::apiResource('campaigns', CampaignController::class);
    Route::patch('campaigns/{campaign}/set-active-status', [CampaignController::class, 'setActiveStatus'])->name('campaigns.set-active-status');

    Route::prefix('campaigns')
        ->controller(CampaignClusterController::class)
        ->group(function () {
            Route::post('{campaign}/clusters', 'store')->name('campaigns.clusters.store');
            Route::put('{campaign}/clusters', 'update')->name('campaigns.clusters.update');
            Route::delete('{campaign}/clusters', 'destroy')->name('campaigns.clusters.destroy');
        });

    Route::apiResource('discounts', DiscountController::class);
    Route::patch('discounts/{discount}/set-active-status', [DiscountController::class, 'setActiveStatus'])->name('discounts.set-active-status');
});
