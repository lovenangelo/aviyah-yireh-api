<?php

use App\Http\Controllers\API\V1\RoleAPIController;
use App\Http\Controllers\API\V1\UserAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\V1\NewPasswordController;
use App\Http\Controllers\Auth\V1\VerifyEmailController;
use App\Http\Controllers\Auth\V1\RegisteredUserController;
use App\Http\Controllers\Auth\V1\PasswordResetLinkController;
use App\Http\Controllers\Auth\V1\AuthenticatedSessionController;
use App\Http\Controllers\Auth\V1\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\V1\TwoFactorAuthController;

Route::prefix('v1')->group(function () {
    // Guest routes
    Route::middleware('guest')->group(function () {
        require_once base_path("routes/API/V1/guest/index.php");
    });

    // Email verification routes
    Route::middleware('auth:sanctum')->group(function () {
        // Logout
        Route::delete('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout')
            ->name('api.verification.send');
        // Email verification
        Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('api.verification.verify');

        // Email verification notification
        Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware(['throttle:6,1']);
    });


    // Authenticated routes
    Route::middleware(['auth:sanctum', 'verified'])->group(function () {
        // User info
        Route::get('/user', function (Request $request) {
            return $request->user();
        })->name('api.user');

        // Two-factor authentication toggle
        Route::post('/two-factor/toggle', [TwoFactorAuthController::class, 'toggle'])->name('api.two-factor.toggle');

        // User profile
        Route::match(['put', 'patch'], '/users/update-profile', [UserAPIController::class, 'updateProfile'])
            ->name('user.profile.update');

        // User password
        Route::match(['put', 'patch'], '/users/update-password', [UserAPIController::class, 'updatePassword'])
            ->name('user.password.update');

        // User avatar
        Route::match(['put', 'patch'], '/users/upload-avatar', [UserAPIController::class, 'uploadAvatar'])
            ->name('user.avatar.upload');
        Route::delete('/users/delete-avatar', [UserAPIController::class, 'deleteAvatar'])
            ->name('user.avatar.delete');

        // User bulk delete
        Route::delete('/users/bulk-delete', [UserAPIController::class, 'bulkDestroy'])
            ->name('users.bulk-delete');

        // User API resource
        Route::apiResource('/users', UserAPIController::class);

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
    });
});
