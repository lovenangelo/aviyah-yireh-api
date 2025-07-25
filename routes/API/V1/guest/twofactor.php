<?php

use App\Http\Controllers\Auth\V1\TwoFactorAuthController;

// Two-factor authentication verification
Route::post('/two-factor/verify', [TwoFactorAuthController::class, 'verify'])->name('api.two-factor.verify');
