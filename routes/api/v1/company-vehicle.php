<?php

use Illuminate\Support\Facades\Route;

Route::prefix('company-vehicle')->group(function () {
    // Define a constant for the tax route segment
    $companyVehicleRoute = '/{company_vehicle}';

    // GET /company-vehicle - List all company owned vehicle
    Route::get('/', [\App\Http\Controllers\API\V1\CompanyVehicle\CompanyVehicleController::class, 'index']);

    // POST /company-vehicle - Create a company owned vehicle
    Route::post('/', [\App\Http\Controllers\API\V1\CompanyVehicle\CompanyVehicleController::class, 'store']);

    // GET /company-vehicle/{company_vehicle} - Show specific company owned vehicle
    Route::get($companyVehicleRoute, [\App\Http\Controllers\API\V1\CompanyVehicle\CompanyVehicleController::class, 'show']);

    // PUT /company-vehicle/{company_vehicle} - Update specific company owned vehicle
    Route::put($companyVehicleRoute, [\App\Http\Controllers\API\V1\CompanyVehicle\CompanyVehicleController::class, 'update']);

    // PATCH /company-vehicle/{company_vehicle} - Partial update specific company owned vehicle
    Route::patch($companyVehicleRoute, [\App\Http\Controllers\API\V1\CompanyVehicle\CompanyVehicleController::class, 'update']);

    // DELETE /company-vehicle/{company_vehicle} - Delete specific company owned vehicle
    Route::delete($companyVehicleRoute, [\App\Http\Controllers\API\V1\CompanyVehicle\CompanyVehicleController::class, 'destroy']);
});
