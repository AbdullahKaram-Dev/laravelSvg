<?php

namespace Abdullah\LaravelSvg;

use Illuminate\Support\ServiceProvider;

/**
 *
 * @author abdullahkaramdev@gmail.com
 */
class LaravelSvgServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/laravel-svg.php','laravel-svg');
        $this->publishes([__DIR__.'/config/laravel-svg.php' => config_path('laravel-svg.php')]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('laravel-svg', function () {
            return new Services\LaravelSvg();
        });
    }

}
