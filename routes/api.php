<?php

use App\Http\Controllers\Api\V1\AssetCategoryApiController;
use App\Http\Controllers\Api\V1\AssetRequestApiController;
use App\Http\Controllers\Api\V1\FundingPoolApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Version 1
|--------------------------------------------------------------------------
| Read and inspection endpoints for public catalog and authenticated
| asset requests. All endpoints return standardized JSON Resources.
*/

Route::prefix('v1')->name('api.v1.')->group(function () {
    // Public Catalog
    Route::get('categories', [AssetCategoryApiController::class, 'index'])->name('categories.index');
    Route::get('categories/{category}', [AssetCategoryApiController::class, 'show'])->name('categories.show');

    Route::get('funding-pools', [FundingPoolApiController::class, 'index'])->name('funding-pools.index');
    Route::get('funding-pools/{pool}', [FundingPoolApiController::class, 'show'])->name('funding-pools.show');

    // Authenticated Resources
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('requests', [AssetRequestApiController::class, 'index'])->name('requests.index');
        Route::get('requests/{assetRequest}', [AssetRequestApiController::class, 'show'])->name('requests.show');
    });

    // Web-session authenticated alias for testing / SPA / internal tooling
    Route::middleware(['auth'])->group(function () {
        Route::get('session/requests', [AssetRequestApiController::class, 'index'])->name('session.requests.index');
        Route::get('session/requests/{assetRequest}', [AssetRequestApiController::class, 'show'])->name('session.requests.show');
    });
});
