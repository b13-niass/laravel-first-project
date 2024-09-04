<?php

namespace App\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Yaml\Yaml;

class AuthCustomServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $yaml = Yaml::parse(file_get_contents(base_path('services.yaml')));
        foreach ($yaml['Auth'] as $key => $value) {
            $bind = $value[0];
            $this->app->singleton($key, function (Application $app) use ($bind) {
                return new $bind();
            });
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
