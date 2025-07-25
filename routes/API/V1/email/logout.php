<?php

use App\Http\Controllers\API\V1\Auth\AuthenticatedSessionController;

// Logout
Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout')
  ->name('api.verification.send');
