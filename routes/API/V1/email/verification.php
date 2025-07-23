<?php

use App\Http\Controllers\Auth\V1\VerifyEmailController;
use App\Http\Controllers\Auth\V1\EmailVerificationNotificationController;

// Email verification
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
  ->middleware(['signed', 'throttle:6,1'])
  ->name('api.verification.verify');

// Email verification notification
Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
  ->middleware(['throttle:6,1']);
