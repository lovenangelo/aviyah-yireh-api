<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        require_once base_path("routes/API/V1/guest/index.php");
    });

    // Email verification routes
    Route::middleware('auth:sanctum')->group(function () {
        require_once base_path("routes/API/V1/email/index.php");
    });

    // Authenticated routes
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        require_once base_path("routes/API/V1/authenticated/index.php");
    });
});
