<?php
namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Relation\SelectMultiple;
use Anavel\Crud\Repository\Criteria\InArrayCriteria;
use Anavel\Crud\Tests\Models\User;
use Anavel\Crud\Tests\TestBase;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Mockery\Mock;
use Illuminate\Database\Eloquent\Model as LaravelModel;


class SelectMultipleTest extends TestBase
{
    /** @var  SelectMultiple */
    protected $sut;
    /** @var  Mock */
    protected $relationMock;
    /** @var  Mock */
    protected $modelManagerMock;
    /** @var  Mock */
    protected $fieldMock;
    /** @var  Mock */
    protected $modelAbstractorMock;

    protected $wrongConfig;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';
        $this->wrongConfig = require __DIR__ . '/../../wrong-config.php';


        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');
        $this->fieldMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');

        \App::instance('Anavel\Crud\Contracts\Abstractor\ModelFactory', $modelFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\ModelFactory'));
        $modelFactoryMock->shouldReceive('getByClassName')->andReturn($this->modelAbstractorMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Model'));
        $this->relationMock->shouldReceive('getRelated')->andReturn($this->relationMock);

        $this->sut = new SelectMultiple(
            $config['Users']['relations']['posts'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->posts(),
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

        $this->modelManagerMock->shouldReceive('getRepository')->atLeast()->once()
            ->andReturn($repoMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository'));

        $modelMock = $this->mock('Anavel\Crud\Tests\Models\Post');
        $repoMock->shouldReceive('all')->atLeast()->once()
            ->andReturn(new Collection([$modelMock, $modelMock, $modelMock]));

        $modelMock->shouldReceive('getKey');
        $modelMock->shouldReceive('getAttribute')->with('title');

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
        $inputArray = ['user_id' => [1, 3, 4]];

        $this->modelManagerMock->shouldReceive('getRepository')->atLeast()->once()
            ->andReturn($repoMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository'));

        $modelMock = $this->mock('Anavel\Crud\Tests\Models\Post');
        $modelMock->shouldReceive('getKey')->andReturn(1);
        $modelMock->shouldReceive('setAttribute', 'save')->atLeast()->times(3);

        $repoMock->shouldReceive('pushCriteria')->atLeast()->once()
            ->andReturn($repoMock);
        $repoMock->shouldReceive('all')->atLeast()->once()
            ->andReturn(new Collection([$modelMock, $modelMock, $modelMock]));

        $this->sut->persist($inputArray);
    }

    public function test_throws_exception_if_display_is_not_set_in_config()
    {
        $this->setExpectedException('Anavel\Crud\Abstractor\Exceptions\RelationException', 'Display should be set in config');

        $this->sut = new SelectMultiple(
            $this->wrongConfig['Users']['relations']['posts'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->posts(),
            $this->fieldMock
        );
    }



    public function test_throws_exception_if_name_is_not_set_in_config()
    {
        $this->setExpectedException('Anavel\Crud\Abstractor\Exceptions\RelationException', 'Relation name should be set');

        $this->sut = new SelectMultiple(
            $this->wrongConfig['Users']['relations']['relation-without-name'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->posts(),
            $this->fieldMock
        );
    }
}
