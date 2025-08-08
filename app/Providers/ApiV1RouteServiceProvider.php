<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ApiV1RouteServiceProvider extends ServiceProvider
{
    private const API_V1_PREFIX = 'api/v1';
    private const AUTH_SANCTUM = 'auth:sanctum';

    public function boot()
    {
        parent::boot();

        $this->mapApiV1Routes();
    }

    protected function mapApiV1Routes()
    {
        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM, 'verified'])
            ->group(base_path('routes/api/v1/auth.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/email.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', 'guest'])
            ->group(base_path('routes/api/v1/guest.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/events.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/training-materials.php'));
        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/logs.php'));
        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/post.php'));
    }
}
