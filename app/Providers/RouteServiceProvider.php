<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Le chemin de base pour votre application.
     */
    public const HOME = '/home';

    /**
     * Définir les routes pour l'application.
     */
    public function boot()
    {
        $this->routes(function () {
            // Route API
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Route Web
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
