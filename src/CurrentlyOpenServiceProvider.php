<?php

namespace Royscheepens\CurrentlyOpen;

use Illuminate\Support\ServiceProvider;

class CurrentlyOpenServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/currently-open.php' => config_path('currently-open.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrentlyOpen::class, function () {
            return new CurrentlyOpen();
        });

        $this->app->alias(CurrentlyOpen::class, 'currently-open');

        $this->mergeConfigFrom(
            __DIR__.'/config/currently-open.php', 'currently-open'
        );
    }
}
