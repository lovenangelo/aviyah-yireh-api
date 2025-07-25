<?php

use App\Http\Controllers\API\V1\Auth\TwoFactorAuthController;

// Two-factor authentication verification
Route::post('/two-factor/verify', [TwoFactorAuthController::class, 'verify'])->name('api.two-factor.verify');
