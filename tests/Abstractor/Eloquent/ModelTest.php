<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Mockery\Mock;


class ModelTest extends TestBase
{
    /** @var  Model */
    protected $sut;

    /** @var Mock */
    protected $dbalMock;
    /** @var Mock */
    protected $columnMock;
    /** @var Mock */
    protected $relationMock;
    /** @var Mock */
    protected $generatorMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->dbalMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer');
        $this->relationMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory');
        $this->columnMock = $this->mock('Doctrine\DBAL\Schema\Column');
        $this->generatorMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Form\Generator');

        $this->sut = new Model($config['Users'], $this->dbalMock, $this->relationMock, $this->generatorMock);
    }

    public function test_implements_model_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model', $this->sut);
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

        $fields = $this->sut->getListFields();

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

        $fields = $this->sut->getDetailFields();

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

        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields);

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_returns_relations_as_array()
    {
        $this->relationMock->shouldReceive('setModel')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('setConfig')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('get')->andReturn($this->mock('\ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Relation'));

        $relations = $this->sut->getRelations();

        $this->assertInternalType('array', $relations, 'Relations is not an array');

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $relations[0]);
    }

    public function test_get_form()
    {
        $this->generatorMock->shouldReceive('setModelFields', 'setRelatedModelFields')
            ->atLeast()->once();
        $this->generatorMock->shouldReceive('getForm')
            ->atLeast()
            ->once()
            ->andReturn($this->mock('FormManager\ElementInterface'));

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

        $this->relationMock->shouldReceive('setModel')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('setConfig')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('get')->andReturn($this->mock('\ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Relation'));



        $form = $this->sut->getForm('crudoado.model.store', 'users');

        $this->assertInstanceOf('FormManager\ElementInterface', $form);
    }



    public function test_get_form_with_instance()
    {
        $this->generatorMock->shouldReceive('setModelFields', 'setRelatedModelFields')
            ->atLeast()->once();
        $this->generatorMock->shouldReceive('getForm')
            ->atLeast()
            ->once()
            ->andReturn($this->mock('FormManager\ElementInterface'));
        $this->generatorMock->shouldReceive('setModel')
            ->atLeast()
            ->once();

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

        $this->relationMock->shouldReceive('setModel')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('setConfig')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('get')->andReturn($this->mock('\ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Relation'));

        $modelMock = $this->mock('Illuminate\Database\Eloquent\Model');
        $modelMock->shouldReceive('getAttribute');

        $this->sut->setInstance($modelMock);


        $form = $this->sut->getForm('crudoado.model.store', 'users');

        $this->assertInstanceOf('FormManager\ElementInterface', $form);
    }

    public function test_get_validation_rules()
    {
        $this->generatorMock->shouldReceive('getValidationRules')
            ->atLeast()->once()
            ->andReturn([]);

        $rules = $this->sut->getValidationRules();

        $this->assertInternalType('array', $rules);
    }

    public function test_persist()
    {
        \App::instance('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager', $modelManagerMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'));
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('input');

        $modelManagerMock->shouldReceive('getModelInstance')->andReturn($userMock = $this->mock('Crudoado\Tests\Models\User'));

        $userMock->shouldReceive('setAttribute', 'save');

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

        $this->relationMock->shouldReceive('setModel')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('setConfig')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('get')->andReturn($relationMock = $this->mock('\ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('persist');

        $result = $this->sut->persist($requestMock);

        $this->assertInstanceOf('Crudoado\Tests\Models\User', $result);
    }
}
