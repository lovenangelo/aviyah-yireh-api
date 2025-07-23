<?php

use App\Http\Controllers\Auth\TwoFactorAuthController;

// Two-factor authentication toggle
Route::post('/two-factor/toggle', [TwoFactorAuthController::class, 'toggle'])->name('api.two-factor.toggle');
