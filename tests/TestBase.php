<?php
namespace Crudoado\Tests;

use Orchestra\Testbench\TestCase;
use DB;

class TestBase extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [];
    }

    protected function getEnvironmentSetUp($app)
    {

    }

    public function testBase()
    {
        $this->assertEquals(true, true);
    }
}
