
<?php

use App\Http\Controllers\API\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\API\V1\Role\RoleAPIController;
use App\Http\Controllers\API\V1\User\UserAPIController;
use App\Http\Controllers\API\V1\Auth\TwoFactorAuthController;

use App\Http\Controllers\API\V1\Event\EventController;
use App\Http\Controllers\API\V1\Event\UserEventsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix("event")->group(function () {
    $eventIdRoute = "/{id}";

    Route::get('/users', [UserEventsController::class, 'index'])->name('users.events.list');
    Route::get('/user/{id}', [UserEventsController::class, 'show'])->name('user.event.retrieve');

    Route::match(['put', 'patch'], '/bulk-delete', [EventController::class, 'bulkDestroy'])->name('event.bulk.delete');
    Route::match(['put', 'patch'], $eventIdRoute, [EventController::class, 'update'])->name('event.update');

    Route::get($eventIdRoute, [EventController::class, 'show'])->name('event.retrieve');
    Route::delete($eventIdRoute, [EventController::class, 'destroy'])->name('event.delete');
    Route::apiResource('/', EventController::class);
});
