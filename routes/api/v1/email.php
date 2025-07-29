<?php

use App\Http\Controllers\API\V1\Auth\VerifyEmailController;
use App\Http\Controllers\API\V1\Auth\EmailVerificationNotificationController;
use Illuminate\Support\Facades\Route;

// Email verification
Route::post('/verify-email', [VerifyEmailController::class, 'store'])
    ->middleware(['throttle:6,1'])
    ->name('api.verification.verify');

// Email verification notification
Route::get('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['throttle:6,1']);
