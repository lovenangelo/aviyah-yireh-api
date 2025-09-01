<?php

use App\Http\Controllers\API\V1\Logs\ActivityController;
use Illuminate\Support\Facades\Route;

Route::prefix('tax')->group(function () {
    Route::apiResource('/', \App\Http\Controllers\API\V1\Tax\TaxController::class);
});
