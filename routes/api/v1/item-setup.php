<?php

use Illuminate\Support\Facades\Route;

Route::prefix('item-setup')->group(function () {
    // Define a constant for the tax route segment
    $itemSetUpRoute = '/{item_setup}';

    // GET /item-setup - List all item categories
    Route::get('/', [\App\Http\Controllers\API\V1\ItemSetUp\ItemSetUpController::class, 'index']);

    // POST /item-setup - Create a new item category
    Route::post('/', [\App\Http\Controllers\API\V1\ItemSetUp\ItemSetUpController::class, 'store']);

    // GET /item-setup/{item-setup} - Show specific item category
    Route::get($itemSetUpRoute, [\App\Http\Controllers\API\V1\ItemSetUp\ItemSetUpController::class, 'show']);

    // PUT /item-setup/{item-setup} - Update specific item category
    Route::put($itemSetUpRoute, [\App\Http\Controllers\API\V1\ItemSetUp\ItemSetUpController::class, 'update']);

    // PATCH /item-setup/{item-setup} - Partial update specific item category
    Route::patch($itemSetUpRoute, [\App\Http\Controllers\API\V1\ItemSetUp\ItemSetUpController::class, 'update']);

    // DELETE /item-setup/{item-setup} - Delete specific item category
    Route::delete($itemSetUpRoute, [\App\Http\Controllers\API\V1\ItemSetUp\ItemSetUpController::class, 'destroy']);
});
