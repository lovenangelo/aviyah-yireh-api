<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\V1\AuthenticatedSessionController;
use App\Http\Controllers\Auth\V1\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\V1\NewPasswordController;
use App\Http\Controllers\Auth\V1\PasswordResetLinkController;
use App\Http\Controllers\Auth\V1\RegisteredUserController;
use App\Http\Controllers\Auth\V1\VerifyEmailController;
use App\Http\Controllers\Auth\V1\TwoFactorAuthController;

Route::prefix('v1')->group(function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('guest')
        ->name('v1.register');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('guest')
        ->name('v1.login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
        ->middleware('guest')
        ->name('v1.password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
        ->middleware('guest')
        ->name('v1.password.store');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['auth', 'signed', 'throttle:6,1'])
        ->name('v1.verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware(['auth', 'throttle:6,1'])
        ->name('v1.verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->middleware('auth')
        ->name('v1.logout');

    // Two-factor authentication routes
    Route::post('/two-factor/verify', [TwoFactorAuthController::class, 'verify'])
        ->middleware('guest')
        ->name('v1.two-factor.verify');

    Route::post('/two-factor/toggle', [TwoFactorAuthController::class, 'toggle'])
        ->middleware('auth')
        ->name('v1.two-factor.toggle');
});
