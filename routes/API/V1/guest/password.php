<?php

use App\Http\Controllers\Auth\V1\PasswordResetLinkController;
use App\Http\Controllers\Auth\V1\NewPasswordController;

// Password reset
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('api.password.email');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('api.password.update');
