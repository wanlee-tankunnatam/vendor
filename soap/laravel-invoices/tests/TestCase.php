<?php

namespace Soap\Invoices\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use \Orchestra\Testbench\TestCase as Orchestra;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Soap\Invoices\Tests\CreateTestModelsTable;

class TestCase extends Orchestra
{

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }


    /**
     * Load any providers I am offering
     * Status Ok; we have to speficay full path
     */
    protected function getPackageProviders($app)
    {
        return
            [
                \Soap\Invoices\Providers\InvoiceServiceProvider::class,
            ];
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('migrate', [
            '--database' => 'sqlite',
            '--realpath' => realpath(__DIR__ . '/../migrations')
        ])->run();
        
        $this->setupDatabase($this->app);
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
    }

    /**
     * Create addional database tables for testing purpose
     * @param mixed $app 
     * @return void 
     */
    protected function setupDatabase($app) 
    {
        include_once __DIR__.'/../database/migrations/2020_02_10_163005_create_invoices_tables.php';

        (new \CreateInvoicesTables())->up();
        
        (new CreateTestModelsTable())->up();

    }
}
