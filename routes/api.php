<?php

use App\Http\Controllers\API\V1\RoleAPIController;
use App\Http\Controllers\API\V1\UserAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\V1\NewPasswordController;
use App\Http\Controllers\Auth\V1\VerifyEmailController;
use App\Http\Controllers\Auth\V1\RegisteredUserController;
use App\Http\Controllers\Auth\V1\PasswordResetLinkController;
use App\Http\Controllers\Auth\V1\AuthenticatedSessionController;
use App\Http\Controllers\Auth\V1\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\V1\TwoFactorAuthController;

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
