<?php

namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Model;
use Anavel\Crud\Tests\TestBase;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Mockery\Mock;
use phpmock\mockery\PHPMockery;

class ModelTest extends TestBase
{
    /** @var Model */
    protected $sut;

    /** @var Mock */
    protected $dbalMock;
    /** @var Mock */
    protected $columnMock;
    /** @var Mock */
    protected $relationFactoryMock;
    /** @var Mock */
    protected $fieldFactoryMock;
    /** @var Mock */
    protected $generatorMock;

    protected $getClassMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__.'/../../config.php';

        $this->dbalMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer');
        $this->relationFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\RelationFactory');
        $this->fieldFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');
        $this->columnMock = $this->mock('Doctrine\DBAL\Schema\Column');
        $this->generatorMock = $this->mock('Anavel\Crud\Contracts\Form\Generator');


        $this->getClassMock = PHPMockery::mock('Anavel\Crud\Abstractor\Eloquent', 'get_class');

        $this->sut = \Mockery::mock(Model::class, [$config['Users'], $this->dbalMock, $this->relationFactoryMock, $this->fieldFactoryMock, $this->generatorMock])->makePartial();
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
                'active'   => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->relationFactoryMock->shouldReceive('setModel')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('setConfig')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('get')
            ->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
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
                'active'   => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->relationFactoryMock->shouldReceive('setModel')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('setConfig')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('get')
            ->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
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
                'active'   => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->relationFactoryMock->shouldReceive('setModel')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('setConfig')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('get')
            ->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
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
                'active'   => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->relationFactoryMock->shouldReceive('setModel')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('setConfig')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('get')
            ->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
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
                'image'    => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $this->relationFactoryMock->shouldReceive('setModel')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('setConfig')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('get')
            ->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        // Don't know of a better way to test ->with() (in the next block) without doing this in the andReturn()
        $this->fieldFactoryMock->shouldReceive('setColumn')
            ->andReturn(
                $fielMock1 = clone $this->fieldFactoryMock,
                $fielMock2 = clone $this->fieldFactoryMock,
                $fielMock3 = clone $this->fieldFactoryMock,
                $fielMock4 = clone $this->fieldFactoryMock,
                $fielMock5 = clone $this->fieldFactoryMock
            );

        //Values from config in setup
        $fielMock1->shouldReceive('setConfig')
            ->with([
                'name'         => 'id',
                'presentation' => null,
                'form_type'    => null,
                'validation'   => null,
                'functions'    => null,
            ])
            ->andReturn($this->fieldFactoryMock);

        //Values from config in setup
        $fielMock2->shouldReceive('setConfig')
            ->with([
                'name'         => 'username',
                'presentation' => null,
                'form_type'    => 'email',
                'validation'   => 'required|email',
                'functions'    => null,
                'defaults'     => 'Chompy',
            ])
            ->andReturn($this->fieldFactoryMock);

        //Values from config in setup
        $fielMock3->shouldReceive('setConfig')
            ->with([
                'name'         => 'password',
                'presentation' => null,
                'form_type'    => 'password',
                'validation'   => 'required|min:8',
                'functions'    => 'bcrypt',
            ])
            ->andReturn($this->fieldFactoryMock);

        //Values from config in setup
        $fielMock4->shouldReceive('setConfig')
            ->with([
                'name'         => 'image',
                'presentation' => null,
                'form_type'    => 'file',
                'validation'   => null,
                'functions'    => null,
            ])
            ->andReturn($this->fieldFactoryMock);

        //Values set from image (file) field
        // Wit this one we check that file fields generate an extra __delete one
        $fielMock5->shouldReceive('setConfig')
            ->with([
                'name'          => 'image__delete',
                'presentation'  => null,
                'form_type'     => 'checkbox',
                'no_validate'   => true,
                'functions'     => null,
            ])
            ->andReturn($this->fieldFactoryMock);

        $this->fieldFactoryMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields);

        $this->assertCount(5, $fields['main']);

        $this->assertArrayHasKey('username', $fields['main']);
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['main']['username']);
    }

    public function test_returns_edit_fields_as_array_with_key()
    {
        $this->dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'       => $this->columnMock,
                'username' => $this->columnMock,
                'password' => $this->columnMock,
                'image'    => $this->columnMock,
            ]);
        $this->dbalMock->shouldReceive('getTableForeignKeys')
            ->andReturn([]);

        $this->dbalMock->shouldReceive('getModel')
            ->andReturn($this->dbalMock);

        $this->dbalMock->shouldReceive('getKeyName')
            ->andReturn(LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT);

        $this->relationFactoryMock->shouldReceive('setModel')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('setConfig')
            ->andReturn($this->relationFactoryMock)
            ->shouldReceive('get')
            ->andReturn($relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation'));
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
            ->andReturn($this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fields = $this->sut->getEditFields(false, 'chompy');

        $this->assertInternalType('array', $fields);

        $this->assertCount(5, $fields['chompy']);

        $this->assertArrayHasKey('username', $fields['chompy']);
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['chompy']['username']);
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
                'image'    => $this->columnMock,
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

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
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
                'image'    => $this->columnMock,
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

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
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
        \App::instance('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager',
            $modelManagerMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'));
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('input')->andReturn('something', ['somethingElse' => ['someValue']]);

        $modelManagerMock->shouldReceive('getModelInstance')->andReturn($userMock = $this->mock('Anavel\Crud\Tests\Models\User'));

        $fieldMock = $this->mock('Anavel\Crud\Abstractor\Eloquent\Field');
        $this->sut->shouldReceive('getEditFields')->times(1)->with(true)->andReturn([
            'main' => [
                $fieldMock,
                $fieldMock,
                $fieldMock,
                $fieldMock,
            ],
        ]);

        $this->sut->shouldReceive('getRelations')->andReturn(collect(['group' => $relationMock = $this->mock('\Anavel\Crud\Abstractor\Eloquent\Relation\Relation')]));

        $userMock->shouldReceive('setAttribute', 'save')->atLeast()->once();

        $relationMock->shouldReceive('persist');
        $relationMock->shouldReceive('getSecondaryRelations')->andReturn(collect());

        $fieldMock->shouldReceive('getName', 'applyFunctions')->andReturn($fieldMock);
        $fieldMock->shouldReceive('saveIfEmpty')->andReturn(true);
        $fieldMock->shouldReceive('getFormField')->andReturn($fieldMock);

        $this->getClassMock->andReturn('nomatch');



        $result = $this->sut->persist($requestMock);

        $this->assertInstanceOf('Anavel\Crud\Tests\Models\User', $result);
    }
}
