<?php

use App\Http\Controllers\Auth\V1\AuthenticatedSessionController;

// Logout
Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout')
  ->name('api.verification.send');
