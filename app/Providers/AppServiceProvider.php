<?php

namespace App\Providers;

use App\Rules\ContainsValidObject;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Symfony\Component\Yaml\Yaml;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();

        $yaml = Yaml::parse(file_get_contents(base_path('services.yaml')));
        foreach ($yaml['App'] as $key => $value) {
            $bind = $value[0];
//            Log::info([$key, $bind]);
            $this->app->singleton($key, $bind);
        }
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
