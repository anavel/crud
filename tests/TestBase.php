<?php

namespace Anavel\Crud\Tests;

use Mockery;
use Orchestra\Testbench\TestCase;

abstract class TestBase extends TestCase
{
    const MIGRATIONS_PATH = 'tests/migrations';

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        $this->resetDatabase();
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__.'/..';
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'    => 'sqlite',
            'database'  => ':memory:',
            'prefix'    => '',
        ]);

        $app['config']->set('anavel.translation_languages', ['gl', 'en', 'es']);
        $app['config']->set('anavel-crud.models', []);

        \App::bind('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager', function ($app) {
            return \Mockery::mock('ANavallaSuiza\Laravel\Database\Manager\Eloquent\ModelManager');
        });
    }

    private function resetDatabase()
    {
        $artisan = $this->app->make('Illuminate\Contracts\Console\Kernel');
        // Makes sure the migrations table is created
        $artisan->call('migrate', [
            '--path'     => self::MIGRATIONS_PATH,
        ]);
        // We empty all tables
        $artisan->call('migrate:reset');
        // Migrate
        $artisan->call('migrate', [
            '--path'     => self::MIGRATIONS_PATH,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [];
    }

    public function mock($className)
    {
        return Mockery::mock($className);
    }

    /**
     * Test running migration.
     *
     * @test
     */
    public function test_running_migration()
    {
        $migrations = \DB::select('SELECT * FROM migrations');

        $fi = new \FilesystemIterator(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.self::MIGRATIONS_PATH, \FilesystemIterator::SKIP_DOTS);

        $this->assertCount(iterator_count($fi), $migrations);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
