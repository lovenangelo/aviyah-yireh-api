<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\V1\AuthenticatedSessionController;
use App\Http\Controllers\Auth\V1\RegisteredUserController;

// Authentication
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('api.login');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('api.register');
