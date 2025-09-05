<?php

use Illuminate\Support\Facades\Route;

Route::prefix('service')->group(function () {
    // Define a constant for the tax route segment
    $serviceRoute = '/{service}';

    // GET /service - List all services
    Route::get('/', [\App\Http\Controllers\API\V1\Service\ServiceController::class, 'index']);

    // POST /service - Create a new service
    Route::post('/', [\App\Http\Controllers\API\V1\Service\ServiceController::class, 'store']);

    // GET /service/{service} - Show specific service
    Route::get($serviceRoute, [\App\Http\Controllers\API\V1\Service\ServiceController::class, 'show']);

    // PUT /service/{service} - Update specific service
    Route::put($serviceRoute, [\App\Http\Controllers\API\V1\Service\ServiceController::class, 'update']);

    // PATCH /service/{service} - Partial update specific service
    Route::patch($serviceRoute, [\App\Http\Controllers\API\V1\Service\ServiceController::class, 'update']);

    // DELETE /service/{service} - Delete specific service
    Route::delete($serviceRoute, [\App\Http\Controllers\API\V1\Service\ServiceController::class, 'destroy']);
});
