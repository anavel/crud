<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Mockery\Mock;


class ModelTest extends TestBase
{
    /** @var  Model */
    protected $model;

    /** @var Mock */
    protected $dbalMock;
    /** @var Mock */
    protected $columnMock;
    /** @var Mock */
    protected $relationMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->dbalMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer');
        $this->relationMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory');
        $this->columnMock = $this->mock('Doctrine\DBAL\Schema\Column');

        $this->model = new Model($config['Users'], $this->dbalMock, $this->relationMock);
    }

    public function test_implements_model_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model', $this->model);
    }

    public function test_returns_list_fields_as_array()
    {
        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'fullname' => $this->columnMock,
                'active'   => $this->columnMock
            ]);

        $fields = $this->model->getListFields();

        $this->assertInternalType('array', $fields);

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_returns_detail_fields_as_array()
    {
        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'password' => $this->columnMock,
                'fullname' => $this->columnMock,
                'info'     => $this->columnMock,
                'active'   => $this->columnMock
            ]);

        $fields = $this->model->getDetailFields();

        $this->assertInternalType('array', $fields);

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_returns_edit_fields_as_array()
    {
        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'password' => $this->columnMock,
            ]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $fields = $this->model->getEditFields();

        $this->assertInternalType('array', $fields);

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_returns_relations_as_array()
    {
        $this->relationMock->shouldReceive('setModel')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('setConfig')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('get')->andReturn($this->mock('\ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Relation'));

        $relations = $this->model->getRelations();

        $this->assertInternalType('array', $relations, 'Relations is not an array');

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $relations[0]);
    }
}
