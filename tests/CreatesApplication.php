<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use RuntimeException;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        // RefreshDatabase drops every table. Refuse to run against anything that
        // is not clearly a throwaway test database (see phpunit.xml).
        $database = $app['config']->get('database.connections.' . $app['config']->get('database.default') . '.database');
        if (!preg_match('/_testing$/', (string) $database)) {
            throw new RuntimeException("Tests refuse to run against database \"$database\": its name must end in _testing.");
        }

        return $app;
    }
}
