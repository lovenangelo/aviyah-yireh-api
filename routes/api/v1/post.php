<?php



use App\Http\Controllers\API\V1\Post\PostController;

use Illuminate\Support\Facades\Route;


Route::prefix('')->group(function () {
    // Standard CRUD routes
    Route::apiResource('posts', PostController::class);

    // Additional endpoints
    Route::get('posts/published', [PostController::class, 'published']);
    Route::delete('posts/bulk-delete', [PostController::class, 'bulkDelete']);
    Route::patch('posts/bulk-update', [PostController::class, 'bulkUpdate']);
});

// Version 2 (future-proofing)
Route::prefix('v2')->group(function () {
    // Route::apiResource('posts', PostV2Controller::class);
});
