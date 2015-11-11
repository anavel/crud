<?php
namespace Crudoado\Tests;

use Orchestra\Testbench\TestCase;
use DB;
use Mockery;

abstract class TestBase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [];
    }

    protected function getEnvironmentSetUp($app)
    {
        \App::bind('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager', function ($app) {
            return \Mockery::mock('ANavallaSuiza\Laravel\Database\Manager\Eloquent\ModelManager');
        });
    }

    public function mock($className)
    {
        return Mockery::mock($className);
    }


    public function tearDown()
    {
        Mockery::close();
    }
}
