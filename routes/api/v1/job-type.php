<?php

use Illuminate\Support\Facades\Route;

Route::prefix('job-type')->group(function () {
    // Define a constant for the tax route segment
    $jobTypeRoute = '/{job_type}';

    // GET /job-type - List all job type
    Route::get('/', [\App\Http\Controllers\API\V1\JobType\JobTypeController::class, 'index']);

    // POST /job-type - Create a new item job type
    Route::post('/', [\App\Http\Controllers\API\V1\JobType\JobTypeController::class, 'store']);

    // GET /job-type/{job_type} - Show specific job type
    Route::get($jobTypeRoute, [\App\Http\Controllers\API\V1\JobType\JobTypeController::class, 'show']);

    // PUT /job-type/{job_type} - Update specific job type
    Route::put($jobTypeRoute, [\App\Http\Controllers\API\V1\JobType\JobTypeController::class, 'update']);

    // PATCH /job-type/{job_type} - Partial update specific job type
    Route::patch($jobTypeRoute, [\App\Http\Controllers\API\V1\JobType\JobTypeController::class, 'update']);

    // DELETE /job-type/{job_type} - Delete specific job type
    Route::delete($jobTypeRoute, [\App\Http\Controllers\API\V1\JobType\JobTypeController::class, 'destroy']);
});
