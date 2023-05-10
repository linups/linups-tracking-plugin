<?php

namespace Linups\TrackingPlugin;

use Illuminate\Support\ServiceProvider;

class TrackingPluginServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('trackingplugin', function($app) {
            return new TrackingPlugin();
        });
    }

    public function boot(): void
    {
        // In addition to publishing assets, we also publish the config
        $this->publishes([
            __DIR__.'/../config/tracking-plugin-config.php' => config_path('tracking-plugin-config.php'),
        ], 'tracking-plugin-config');
    }
}
