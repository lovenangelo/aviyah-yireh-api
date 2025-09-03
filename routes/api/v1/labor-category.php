<?php

use Illuminate\Support\Facades\Route;

Route::prefix('labor-category')->group(function () {
    // Define a constant for the tax route segment
    $laborCategoryRoute = '/{labor_category}';

    // GET /labor-category - List all item categories
    Route::get('/', [\App\Http\Controllers\API\V1\LaborCategory\LaborCategoryController::class, 'index']);

    // POST /labor-category - Create a new item category
    Route::post('/', [\App\Http\Controllers\API\V1\LaborCategory\LaborCategoryController::class, 'store']);

    // GET /labor-category/{labor-category} - Show specific item category
    Route::get($laborCategoryRoute, [\App\Http\Controllers\API\V1\LaborCategory\LaborCategoryController::class, 'show']);

    // PUT /labor-category/{labor-category} - Update specific item category
    Route::put($laborCategoryRoute, [\App\Http\Controllers\API\V1\LaborCategory\LaborCategoryController::class, 'update']);

    // PATCH /labor-category/{labor-category} - Partial update specific item category
    Route::patch($laborCategoryRoute, [\App\Http\Controllers\API\V1\LaborCategory\LaborCategoryController::class, 'update']);

    // DELETE /labor-category/{labor-category} - Delete specific item category
    Route::delete($laborCategoryRoute, [\App\Http\Controllers\API\V1\LaborCategory\LaborCategoryController::class, 'destroy']);
});
