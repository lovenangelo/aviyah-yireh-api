<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\TrainingMaterial\TrainingMaterialAPIController;

Route::prefix("training-materials")->group(function () {
    // Define a constant for the ID route segment
    $id_route = "/{id}";

    // Get a list of training material
    Route::get("/", [TrainingMaterialAPIController::class, 'index'])->name("list.training.materials");

    // Get a training material
    Route::get($id_route, [TrainingMaterialAPIController::class, 'show'])->name("get.training.material");

    // Upload training material
    Route::post("/", [TrainingMaterialAPIController::class, 'store'])->name("upload.training.materials");

    // Edit training material
    Route::put($id_route, [TrainingMaterialAPIController::class, 'update'])->name("edit.training.material");

    // Delete training material
    Route::delete($id_route, [TrainingMaterialAPIController::class, 'destroy'])->name("delete.training.material");

    // Delete training material
    Route::post("/bulk-delete", [TrainingMaterialAPIController::class, 'bulkDestroy'])->name("bulk.delete.training.material");
});
