<?php

use App\Http\Controllers\API\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\API\V1\Auth\NewPasswordController;
use App\Http\Controllers\API\V1\Auth\PasswordResetLinkController;
use App\Http\Controllers\API\V1\Auth\RegisteredUserController;
use App\Http\Controllers\API\V1\Auth\TwoFactorAuthController;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Route;

// Authentication
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('api.login');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('api.register');

// Password reset
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('api.password.email');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('api.password.update');

// Two-factor authentication verification
Route::post('/two-factor/verify', [TwoFactorAuthController::class, 'verify'])->name('api.two-factor.verify');

Route::get('/health', function () {
    return $this->comment(Inspiring::quote());
});
