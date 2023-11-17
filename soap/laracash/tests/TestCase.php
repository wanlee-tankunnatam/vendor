<?php

namespace Soap\Laracash\Tests;

use Orchestra\Testbench\TestCase as Orchestral;

class TestCase extends Orchestral
{
    /**
     * Setup tests
     * @return void 
     */
    public function setup(): void
    {
        Parent::setup();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application    $app
     * @return void
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
