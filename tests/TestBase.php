<?php
namespace Crudoado\Tests;

use Orchestra\Testbench\TestCase;
use DB;
use Mockery;

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
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['path.base'] = __DIR__.'/..';
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'    => ''
        ]);

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
        $fi = new \FilesystemIterator(self::MIGRATIONS_PATH, \FilesystemIterator::SKIP_DOTS);
        $this->assertCount(iterator_count($fi), $migrations);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
