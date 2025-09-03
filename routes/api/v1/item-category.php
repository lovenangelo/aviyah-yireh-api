<?php

use Illuminate\Support\Facades\Route;

Route::prefix('item-category')->group(function () {
    // Define a constant for the tax route segment
    $iCategoryRoute = '/{item_category}';

    // GET /item-category - List all item categories
    Route::get('/', [\App\Http\Controllers\API\V1\ItemCategory\ItemCategoryController::class, 'index']);

    // POST /item-category - Create a new item category
    Route::post('/', [\App\Http\Controllers\API\V1\ItemCategory\ItemCategoryController::class, 'store']);

    // GET /item-category/{item-category} - Show specific item category
    Route::get($iCategoryRoute, [\App\Http\Controllers\API\V1\ItemCategory\ItemCategoryController::class, 'show']);

    // PUT /item-category/{item-category} - Update specific item category
    Route::put($iCategoryRoute, [\App\Http\Controllers\API\V1\ItemCategory\ItemCategoryController::class, 'update']);

    // PATCH /item-category/{item-category} - Partial update specific item category
    Route::patch($iCategoryRoute, [\App\Http\Controllers\API\V1\ItemCategory\ItemCategoryController::class, 'update']);

    // DELETE /item-category/{item-category} - Delete specific item category
    Route::delete($iCategoryRoute, [\App\Http\Controllers\API\V1\ItemCategory\ItemCategoryController::class, 'destroy']);
});
