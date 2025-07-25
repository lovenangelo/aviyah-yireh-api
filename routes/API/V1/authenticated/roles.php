<?php

use App\Http\Controllers\API\V1\Role\RoleAPIController;

// Roles-related Routes
Route::prefix("roles")->group(function () {
  // Roles list
  Route::get("/", [RoleAPIController::class, 'index'])->name('roles.list');

  // Role retrieve
  Route::get("/{id}", [RoleAPIController::class, 'show'])->name("role.retrieve");

  // Role create
  Route::post("/", [RoleAPIController::class, 'store'])->name('roles.create');

  // Role update
  Route::put("/{id}", [RoleAPIController::class, 'update'])->name("roles.update");

  // Role delete
  Route::delete("/{id}", [RoleAPIController::class, 'destroy'])->name("roles.delete");

  // Role bulk delete
  Route::post('/bulk-destroy', [RoleAPIController::class, 'bulkDestroy'])
    ->name('roles.bulk-delete');

  // Role API resource
  Route::apiResource('/', RoleAPIController::class);
});
