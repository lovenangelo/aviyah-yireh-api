<?php

use Illuminate\Support\Facades\Route;

Route::prefix('tax')->group(function () {
    // Define a constant for the tax route segment
    $taxRoute = '/{tax}';

    // GET /tax - List all taxes
    Route::get('/', [\App\Http\Controllers\API\V1\Tax\TaxController::class, 'index']);

    // POST /tax - Create a new tax
    Route::post('/', [\App\Http\Controllers\API\V1\Tax\TaxController::class, 'store']);

    // GET /tax/{tax} - Show specific tax
    Route::get($taxRoute, [\App\Http\Controllers\API\V1\Tax\TaxController::class, 'show']);

    // PUT /tax/{tax} - Update specific tax
    Route::put($taxRoute, [\App\Http\Controllers\API\V1\Tax\TaxController::class, 'update']);

    // PATCH /tax/{tax} - Partial update specific tax
    Route::patch($taxRoute, [\App\Http\Controllers\API\V1\Tax\TaxController::class, 'update']);

    // DELETE /tax/{tax} - Delete specific tax
    Route::delete($taxRoute, [\App\Http\Controllers\API\V1\Tax\TaxController::class, 'destroy']);
});
