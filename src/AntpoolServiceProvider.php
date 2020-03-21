<?php

/**
 * @package aburakovskiy\laravel-antpool-api
 * @author Alexander Burakovskiy <alexander.burakovskiy@gmail.com>
 */
namespace Aburakovskiy\LaravelAntpoolApi;

use Illuminate\Support\ServiceProvider;

class AntpoolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([__DIR__ . '/config/antpool.php' => config_path('antpool.php')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('antpool', function () {
            return new Antpool(config('antpool.username'), config('antpool.api_key'), config('antpool.api_secret'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Antpool::class];
    }
}
