<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class ApiBaseProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for your application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        // Other route mappings
    }

    /**
     * Define the "api" routes for your application.
     *
     * These routes are loaded by the RouteServiceProvider within a group which
     * is assigned the "api" middleware group. Feel free to change the base URL here.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('wane') // Change 'your-new-prefix' to your desired base URL
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
