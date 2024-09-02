<?php

namespace App\Providers;

use App\Rules\ContainsValidObject;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRoutes();
        //        // Set token expiration settings
        //        Passport::tokensExpireIn(now()->addMinutes(1)); // Access token expiry
        //        Passport::refreshTokensExpireIn(now()->addMinutes(1)); // Refresh token expiry
        //        Passport::personalAccessTokensExpireIn(now()->addMinutes(1)); // Personal access token expiry
        //        Validator::extend('contains_valid_object', ContainsValidObject::class);
        //        Rule::macro('uppercase',function () { return new Uppercase();  }
    }

    protected function configureRoutes()
    {
        // Load web routes
        Route::middleware('web')
            ->group(base_path('routes/web.php'));

        // Load API routes
        Route::middleware('api')
            ->prefix('wane')
            ->group(base_path('routes/api.php'));
    }
}
