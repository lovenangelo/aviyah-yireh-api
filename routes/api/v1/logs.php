<?php

use App\Http\Controllers\API\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\API\V1\Role\RoleAPIController;
use App\Http\Controllers\API\V1\User\UserAPIController;
use App\Http\Controllers\API\V1\Auth\TwoFactorAuthController;

use App\Http\Controllers\API\V1\Event\EventController;
use App\Http\Controllers\API\V1\Event\UserEventsController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\V1\Logs\ActivityController;
use Illuminate\Support\Facades\Route;

Route::prefix("logs")->group(function () {
    Route::get('/all', [ActivityController::class, 'getAllLogs']);
});
