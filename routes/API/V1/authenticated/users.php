<?php

use App\Http\Controllers\API\V1\User\UserAPIController;

// User info
Route::get('/user', function (Request $request) {
  return $request->user();
})->name('api.user');

Route::prefix("users")->group(function () {
  // User profile
  Route::match(['put', 'patch'], '/update-profile', [UserAPIController::class, 'updateProfile'])
    ->name('user.profile.update');

  // User password
  Route::match(['put', 'patch'], '/update-password', [UserAPIController::class, 'updatePassword'])
    ->name('user.password.update');

  // User avatar
  Route::match(['put', 'patch'], '/upload-avatar', [UserAPIController::class, 'uploadAvatar'])
    ->name('user.avatar.upload');
  Route::delete('/delete-avatar', [UserAPIController::class, 'deleteAvatar'])
    ->name('user.avatar.delete');

  // User bulk delete
  Route::delete('/bulk-delete', [UserAPIController::class, 'bulkDestroy'])
    ->name('users.bulk-delete');

  // User API resource
  Route::apiResource('/', UserAPIController::class);
});
