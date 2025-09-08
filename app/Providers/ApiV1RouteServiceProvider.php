<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

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
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/auth.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/email.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', 'guest'])
            ->group(base_path('routes/api/v1/guest.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/logs.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/company.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/tax.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/item-category.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/labor-category.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/item.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/service-category.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/service.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', self::AUTH_SANCTUM])
            ->group(base_path('routes/api/v1/company-vehicle.php'));
    }
}
