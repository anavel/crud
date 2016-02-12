<?php
namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Tests\TestBase;
use Anavel\Crud\Abstractor\Eloquent\Model;
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
    protected $relationFactoryMock;
    /** @var Mock */
    protected $fieldMock;
    /** @var Mock */
    protected $generatorMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->dbalMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer');
        $this->relationFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\RelationFactory');
        $this->fieldMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');
        $this->columnMock = $this->mock('Doctrine\DBAL\Schema\Column');
        $this->generatorMock = $this->mock('Anavel\Crud\Contracts\Form\Generator');

        $this->sut = new Model($config['Users'], $this->dbalMock, $this->relationFactoryMock, $this->fieldMock, $this->generatorMock);
    }

    public function test_implements_model_interface()
    {
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Model', $this->sut);
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
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getListFields();

        $this->assertInternalType('array', $fields);

        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['main'][0]);
    }

    public function test_returns_list_fields_as_array_with_key()
    {
        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'fullname' => $this->columnMock,
                'active'   => $this->columnMock
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getListFields('chompy');

        $this->assertInternalType('array', $fields);

        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['chompy'][0]);
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
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getDetailFields();

        $this->assertInternalType('array', $fields);

        $this->assertCount(6, $fields['main']);

        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['main'][0]);
    }

    public function test_returns_detail_fields_as_array_with_key()
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
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getDetailFields('chompy');

        $this->assertInternalType('array', $fields);

        $this->assertCount(6, $fields['chompy']);

        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['chompy'][0]);
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
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields);

        $this->assertCount(3, $fields['main']);

        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['main'][0]);
    }

    public function test_returns_edit_fields_as_array_with_key()
    {
        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'password' => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getEditFields(false, 'chompy');

        $this->assertInternalType('array', $fields);

        $this->assertCount(3, $fields['chompy']);

        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['chompy'][0]);
    }

    public function test_returns_relations_as_collection()
    {
        $this->relationFactoryMock->shouldReceive('setModel')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('setConfig')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('get')->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $relations = $this->sut->getRelations();

        $this->assertInstanceOf('Illuminate\Support\Collection', $relations);

        foreach ($relations as $relation) {
            $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Relation', $relation);
        }

    }

    public function test_returns_relations_as_multidimensional_collection()
    {
        $this->relationFactoryMock->shouldReceive('setModel')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('setConfig')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('get')->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect(['chompy' => $relationMock]));

        $relations = $this->sut->getRelations();

        $this->assertInstanceOf('Illuminate\Support\Collection', $relations);

        foreach ($relations as $relation) {
            $this->assertInstanceOf('Illuminate\Support\Collection', $relation);
            $this->assertArrayHasKey('relation', $relation);
            $this->assertArrayHasKey('secondaryRelations', $relation);
            $this->assertInstanceOf('Illuminate\Support\Collection', $relation->get('secondaryRelations'));
            foreach ($relation->get('secondaryRelations') as $secondaryRelation) {
                $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Relation', $secondaryRelation);
            }
        }

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
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $this->relationFactoryMock->shouldReceive('setModel')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('setConfig')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('get')->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

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

        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'password' => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $this->relationFactoryMock->shouldReceive('setModel')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('setConfig')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('get')->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

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

        $modelManagerMock->shouldReceive('getModelInstance')->andReturn($userMock = $this->mock('Anavel\Crud\Tests\Models\User'));

        $userMock->shouldReceive('setAttribute', 'save');

        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'password' => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $this->relationFactoryMock->shouldReceive('setModel')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('setConfig')->andReturn($this->relationFactoryMock);
        $this->relationFactoryMock->shouldReceive('get')->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('persist');
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $fieldMock = $this->mock('Anavel\Crud\Abstractor\Eloquent\Field');
        $fieldMock->shouldReceive('saveIfEmpty', 'getName', 'applyFunctions');

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')
            ->andReturn($fieldMock);

        $result = $this->sut->persist($requestMock);

        $this->assertInstanceOf('Anavel\Crud\Tests\Models\User', $result);
    }
}
