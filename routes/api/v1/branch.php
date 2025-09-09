<?php

use Illuminate\Support\Facades\Route;

Route::prefix('branch')->group(function () {
    // Define a constant for the tax route segment
    $jobTypeRoute = '/{branch}';

    // GET /branch - List all branches
    Route::get('/', [\App\Http\Controllers\API\V1\Branch\BranchController::class, 'index']);

    // POST /branch - Create a new item branch
    Route::post('/', [\App\Http\Controllers\API\V1\Branch\BranchController::class, 'store']);

    // GET /branch/{branch} - Show specific branch
    Route::get($jobTypeRoute, [\App\Http\Controllers\API\V1\Branch\BranchController::class, 'show']);

    // PUT /branch/{branch} - Update specific branch
    Route::put($jobTypeRoute, [\App\Http\Controllers\API\V1\Branch\BranchController::class, 'update']);

    // PATCH /branch/{branch} - Partial update specific branch
    Route::patch($jobTypeRoute, [\App\Http\Controllers\API\V1\Branch\BranchController::class, 'update']);

    // DELETE /branch/{branch} - Delete specific branch
    Route::delete($jobTypeRoute, [\App\Http\Controllers\API\V1\Branch\BranchController::class, 'destroy']);
});
