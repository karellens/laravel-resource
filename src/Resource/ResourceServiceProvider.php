<?php

namespace Karellens\Resource;

use Illuminate\Support\ServiceProvider;

class ResourceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([realpath(__DIR__.'/../../config/api.php') => config_path('api.php')]);
        $this->publishes([realpath(__DIR__.'/../../config/rules.php') => config_path('rules.php')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        include __DIR__.'/routes.php';

        $this->app->singleton('Karellens\Resource\QueryMap', function ($app) {
            return new \Karellens\Resource\QueryMap;
        });
        $this->app->singleton('Karellens\Resource\Rules', function ($app) {
            return new \Karellens\Resource\Rules;
        });
        $this->app->singleton('Karellens\Resource\ApiResponse', function ($app) {
            return new \Karellens\Resource\ApiResponse;
        });
    }
}
