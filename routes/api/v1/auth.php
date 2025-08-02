<?php

use App\Http\Controllers\API\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\API\V1\Role\RoleAPIController;
use App\Http\Controllers\API\V1\User\UserAPIController;
use App\Http\Controllers\API\V1\Auth\TwoFactorAuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Logout
Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy']);

// Roles-related Routes
Route::prefix("roles")->group(function () {
    // Define a constant for the ID route segment
    $roleIdRoute = '/{id}';

    // Roles list
    Route::get("/", [RoleAPIController::class, 'index'])->name('roles.list');

    // Role retrieve
    Route::get($roleIdRoute, [RoleAPIController::class, 'show'])->name("role.retrieve");

    // Role create
    Route::post("/", [RoleAPIController::class, 'store'])->name('roles.create');

    // Role update
    Route::put($roleIdRoute, [RoleAPIController::class, 'update'])->name("roles.update");

    // Role delete
    Route::delete($roleIdRoute, [RoleAPIController::class, 'destroy'])->name("roles.delete");

    // Role bulk delete
    Route::post('/bulk-destroy', [RoleAPIController::class, 'bulkDestroy'])
        ->name('roles.bulk-delete');
});


// Two-factor authentication toggle
Route::post('/two-factor/toggle', [TwoFactorAuthController::class, 'toggle']);

// User info
Route::get('/me', [UserAPIController::class, 'me'])->name('api.user');

Route::prefix("users")->group(function () {
    // Define a constant for the ID route segment
    $userIdRoute = '/{id}';

    // User profile
    Route::match(['put', 'patch'], '/update-profile', [UserAPIController::class, 'updateProfile'])
        ->name('user.profile.update');

    // User password
    Route::match(['put', 'patch'], '/update-password', [UserAPIController::class, 'updatePassword'])
        ->name('user.password.update');

    // User avatar
    Route::post('/upload-avatar', [UserAPIController::class, 'uploadAvatar'])
        ->name('user.avatar.upload');
    Route::delete('/delete-avatar', [UserAPIController::class, 'deleteAvatar'])
        ->name('user.avatar.delete');

    // User bulk delete
    Route::delete('/bulk-delete', [UserAPIController::class, 'bulkDestroy'])
        ->name('users.bulk-delete');

    // User list
    Route::get("/", [UserAPIController::class, 'index'])->name('users.list');

    // User retrieve
    Route::get($userIdRoute, [UserAPIController::class, 'show'])->name("user.retrieve");

    // User delete
    Route::delete($userIdRoute, [UserAPIController::class, 'destroy'])->name("users.delete");
});
