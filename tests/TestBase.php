<?php
namespace Crudoado\Tests;

use Orchestra\Testbench\TestCase;
use DB;

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
}
