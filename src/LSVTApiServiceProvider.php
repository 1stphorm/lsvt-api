<?php

namespace PhormDev\LSVT;

use Illuminate\Foundation\Application;

class LSVTApiServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/lsvt.php', 'lsvt'
        );

        $this->publishes([
            __DIR__.'/../config/lsvt.php' => config_path('lsvt.php'),
        ], 'lsvt-config');

        $this->app->singleton(API::class, function (Application $app) {
            return new API(
                $app->make(\GuzzleHttp\Client::class),
                $app['config']['lsvt']['username'],
                $app['config']['lsvt']['password'],
                $app['config']['lsvt']['url'],
            );
        });

    }
}
