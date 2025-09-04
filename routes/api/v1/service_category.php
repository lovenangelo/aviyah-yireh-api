<?php

use Illuminate\Support\Facades\Route;

Route::prefix('service-category')->group(function () {
    // Define a constant for the tax route segment
    $serviceCategoryRoute = '/{service_category}';

    // GET /service-category - List all service categories
    Route::get('/', [\App\Http\Controllers\API\V1\ServiceCategory\ServiceCategoryController::class, 'index']);

    // POST /service-category - Create a new service category
    Route::post('/', [\App\Http\Controllers\API\V1\ServiceCategory\ServiceCategoryController::class, 'store']);

    // GET /service-category/{service-category} - Show specific service category
    Route::get($serviceCategoryRoute, [\App\Http\Controllers\API\V1\ServiceCategory\ServiceCategoryController::class, 'show']);

    // PUT /service-category/{service-category} - Update specific service category
    Route::put($serviceCategoryRoute, [\App\Http\Controllers\API\V1\ServiceCategory\ServiceCategoryController::class, 'update']);

    // PATCH /service-category/{service-category} - Partial update specific service category
    Route::patch($serviceCategoryRoute, [\App\Http\Controllers\API\V1\ServiceCategory\ServiceCategoryController::class, 'update']);

    // DELETE /service-category/{service-category} - Delete specific service category
    Route::delete($serviceCategoryRoute, [\App\Http\Controllers\API\V1\ServiceCategory\ServiceCategoryController::class, 'destroy']);
});
