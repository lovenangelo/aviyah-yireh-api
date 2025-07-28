<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\TrainingMaterial\TrainingMaterialAPIController;

Route::prefix("training-materials")->group(function () {
  // Get a list of training material
  Route::get("/", [TrainingMaterialAPIController::class, 'index'])->name("list.training.materials");
});
