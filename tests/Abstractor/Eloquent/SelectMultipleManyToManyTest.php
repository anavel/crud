<?php

namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Relation\SelectMultipleManyToMany;
use Anavel\Crud\Tests\Models\User;
use Anavel\Crud\Tests\TestBase;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\Mock;
use phpmock\mockery\PHPMockery;

class SelectMultipleManyToManyTest extends TestBase
{
    /** @var SelectMultipleManyToMany */
    protected $sut;
    /** @var Mock */
    protected $relationMock;
    /** @var Mock */
    protected $modelManagerMock;
    /** @var Mock */
    protected $fieldMock;
    /** @var Mock */
    protected $modelAbstractorMock;
    /** @var Mock */
    protected $requestMock;

    protected $wrongConfig;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__.'/../../config.php';
        $this->wrongConfig = require __DIR__.'/../../wrong-config.php';


        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');
        $this->fieldMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');
        $this->requestMock = $this->mock('Illuminate\Http\Request');

        \App::instance('Anavel\Crud\Contracts\Abstractor\ModelFactory', $modelFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\ModelFactory'));
        $modelFactoryMock->shouldReceive('getByClassName')->andReturn($this->modelAbstractorMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Model'));
        $this->relationMock->shouldReceive('getRelated')->andReturn($this->relationMock);

        $getClassMock = PHPMockery::mock('Anavel\Crud\Abstractor\Eloquent\Relation\Traits',
            'get_class');

        $getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\BelongsToMany');

        $this->sut = new SelectMultipleManyToMany(
            $config['Users']['relations']['roles'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->roles(),
            $this->fieldMock
        );
    }

    public function test_implements_relation_interface()
    {
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_get_edit_fields_returns_array_of_fields_with_proper_key()
    {
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->andReturn($dbalMock = $this->mock('\ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));
        $dbalMock->shouldReceive('getTableColumn')->andReturn($columnMock = $this->mock('Doctrine\DBAL\Schema\Column'));
        $columnMock->shouldReceive('getName');

        $this->modelManagerMock->shouldReceive('getRepository')->atLeast()->once()
            ->andReturn($repoMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository'));

        $modelMock = $this->mock('Anavel\Crud\Tests\Models\Role');
        $repoMock->shouldReceive('all')->atLeast()->once()
            ->andReturn(new Collection([$modelMock, $modelMock, $modelMock]));

        $modelMock->shouldReceive('getKey');
        $modelMock->shouldReceive('getAttribute')->with('name');

        $this->fieldMock->shouldReceive('setColumn', 'setConfig')->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')->andReturn($field = $this->mock('Anavel\Crud\Contracts\Abstractor\Field'));

        $field->shouldReceive('setOptions');

        $fields = $this->sut->getEditFields('chompy');

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(1, $fields['chompy']);

        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['chompy'][0]);
    }

    public function test_persist()
    {
        $inputArray = ['id' => [1, 3, 4]];
        $config = require __DIR__.'/../../config.php';

        $relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');
        $relationMock->shouldReceive('getRelated')->andReturn($relationMock);
        $relationMock->shouldReceive('getKeyName')->andReturn('id');
        $relationMock->shouldReceive('sync');

        $this->sut = new SelectMultipleManyToMany(
            $config['Users']['relations']['roles'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $relationMock,
            $this->fieldMock
        );

        $this->sut->persist($inputArray, $this->requestMock);
    }

    public function test_throws_exception_if_display_is_not_set_in_config()
    {
        $this->setExpectedException('Anavel\Crud\Abstractor\Exceptions\RelationException', 'Display should be set in config');

        $this->sut = new SelectMultipleManyToMany(
            $this->wrongConfig['Users']['relations']['roles'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->roles(),
            $this->fieldMock
        );
    }

    public function test_throws_exception_if_name_is_not_set_in_config()
    {
        $this->setExpectedException('Anavel\Crud\Abstractor\Exceptions\RelationException', 'Relation name should be set');

        $this->sut = new SelectMultipleManyToMany(
            $this->wrongConfig['Users']['relations']['relation-without-name'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->roles(),
            $this->fieldMock
        );
    }
}
