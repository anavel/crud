<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;
use Crudoado\Tests\TestBase;
use Mockery;

class RelationTest extends TestBase
{
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__.'/../../config.php';

        $this->model = new Relation();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_implements_relation_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $this->model);
    }
}
