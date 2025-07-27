<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ApiV1RouteServiceProvider extends ServiceProvider
{
    private const API_V1_PREFIX = 'api/v1';

    public function boot()
    {
        parent::boot();

        $this->mapApiV1Routes();
    }

    protected function mapApiV1Routes()
    {
        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', 'auth:sanctum', 'verified'])
            ->group(base_path('routes/api/v1/authenticated.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', 'auth:sanctum'])
            ->group(base_path('routes/api/v1/email.php'));

        Route::prefix(self::API_V1_PREFIX)
            ->middleware(['api', 'guest'])
            ->group(base_path('routes/api/v1/guest.php'));
    }
}
