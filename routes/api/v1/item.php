<?php

use Illuminate\Support\Facades\Route;

Route::prefix('item')->group(function () {
    // Define a constant for the tax route segment
    $itemRoute = '/{item}';

    // GET /item - List all item categories
    Route::get('/', [\App\Http\Controllers\API\V1\Item\ItemController::class, 'index']);

    // POST /item - Create a new item category
    Route::post('/', [\App\Http\Controllers\API\V1\Item\ItemController::class, 'store']);

    // GET /item/{item} - Show specific item category
    Route::get($itemRoute, [\App\Http\Controllers\API\V1\Item\ItemController::class, 'show']);

    // PUT /item/{item} - Update specific item category
    Route::put($itemRoute, [\App\Http\Controllers\API\V1\Item\ItemController::class, 'update']);

    // PATCH /item/{item} - Partial update specific item category
    Route::patch($itemRoute, [\App\Http\Controllers\API\V1\Item\ItemController::class, 'update']);

    // DELETE /item/{item} - Delete specific item category
    Route::delete($itemRoute, [\App\Http\Controllers\API\V1\Item\ItemController::class, 'destroy']);
});
