<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use PhormDev\LSVT\LSVTApiServiceProvider;

class TestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LSVTApiServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('lsvt.username', '');
        $app['config']->set('lsvt.password', '');
    }
}
