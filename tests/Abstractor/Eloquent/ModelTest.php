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

        $this->model = new Model($config['Users'], $this->getDbalMock());
    }

    public function tearDown()
    {
        Mockery::close();
    }

    private function getDbalMock()
    {
        return Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer');
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

    public function test_returns_relations_as_array()
    {
        $relations = $this->model->getEditRelations();

        $this->assertInternalType('array', $relations, 'Relations is not an array');
    }

    public function test_elements_of_relations_array_are_instances_of_relation()
    {
        $relations = $this->model->getEditRelations();

        foreach ($relations as $relation) {
            $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $relation);
        }

    }
}
