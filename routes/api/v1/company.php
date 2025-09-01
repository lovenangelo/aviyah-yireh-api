<?php

use App\Http\Controllers\API\V1\Logs\ActivityController;
use Illuminate\Support\Facades\Route;

Route::prefix('company')->group(function () {
    Route::apiResource('/', \App\Http\Controllers\API\V1\Company\CompanyController::class);
});
