<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\TrainingMaterial\TrainingMaterialAPIController;

Route::prefix("training-materials")->group(function () {
  // Get a list of training material
  Route::get("/", [TrainingMaterialAPIController::class, 'index'])->name("list.training.materials");

  // Get a training material
  Route::get("/{id}", [TrainingMaterialAPIController::class, 'show'])->name("get.training.material");

  // Upload training material
  Route::post("/", [TrainingMaterialAPIController::class, 'store'])->name("upload.training.materials");

  // Edit training material
  Route::put("/{id}", [TrainingMaterialAPIController::class, 'update'])->name("edit.training.material");
});
