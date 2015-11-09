<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use Mockery;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model;

class ModelTest extends TestBase
{
    /** @var  Model */
    protected $model;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__.'/../../config.php';

        $this->model = new Model($config, $this->getModelManagerMock());
    }

    public function tearDown()
    {
        Mockery::close();
    }

    private function getModelManagerMock()
    {
        return Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
    }

    public function test_implements_model_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model', $this->model);
    }

    public function test_loads_model_by_slug()
    {

    }

    public function test_loads_model_by_name()
    {

    }

    public function test_returns_list_fields_as_array()
    {
    }

    public function test_returns_detail_fields_as_array()
    {

    }

    public function test_returns_edit_fields_as_array()
    {

    }

    public function test_returns_relations_fields_as_array()
    {

    }
}
