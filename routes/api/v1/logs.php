<?php

use App\Http\Controllers\API\V1\Logs\ActivityController;
use Illuminate\Support\Facades\Route;

Route::prefix('logs')->group(function () {
    Route::get('/all', [ActivityController::class, 'getAllLogs']);
});
