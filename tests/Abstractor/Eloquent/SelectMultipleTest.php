<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\SelectMultiple;
use ANavallaSuiza\Crudoado\Repository\Criteria\InArrayCriteria;
use Crudoado\Tests\Models\User;
use Crudoado\Tests\TestBase;
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

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');

        $this->sut = new SelectMultiple(
            $config['Users']['relations']['posts'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->posts()
        );
    }

    public function test_implements_relation_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_get_edit_fields_returns_array_of_fields_with_proper_key()
    {
        $relationFactoryMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory');
        $modelFactoryMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory');

        $columnMock = $this->mock('Doctrine\DBAL\Schema\Column');
        $fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field');

        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory', $relationFactoryMock);
        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory', $modelFactoryMock);

        $modelFactoryMock->shouldReceive('getByClassName')
            ->andReturn($this->modelManagerMock);

        $this->modelManagerMock->shouldReceive('getEditFields')->atLeast()->once()
            ->andReturn([$fieldMock, $fieldMock, $fieldMock]);

        $this->modelManagerMock->shouldReceive('getRepository')->atLeast()->once()
            ->andReturn($repoMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository'));

        $modelMock = $this->mock('Crudoado\Tests\Models\Post');
        $repoMock->shouldReceive('all')->atLeast()->once()
            ->andReturn(new Collection([$modelMock, $modelMock, $modelMock]));

        $modelMock->shouldReceive('getKey');
        $modelMock->shouldReceive('getAttribute')->with('title');

        $fieldMock->shouldReceive('getName')->atLeast()->once()->andReturn('user_id', 'user_id', 'chompy');
        $fieldMock->shouldReceive('setName')->atLeast()->once()->with(matchesPattern("/^posts\[user_id\]\[\]/"));
        $fieldMock->shouldReceive('setOptions', 'setCustomFormType');

        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(1, $fields);

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_persist()
    {
        $inputArray = ['user_id' => [1, 3, 4]];
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('input')->with('posts')->atLeast()->once()->andReturn($inputArray);

        $this->modelManagerMock->shouldReceive('getRepository')->atLeast()->once()
            ->andReturn($repoMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository'));

        $modelMock = $this->mock('Crudoado\Tests\Models\Post');
        $modelMock->shouldReceive('setAttribute', 'save')->times(3);

        $repoMock->shouldReceive('pushCriteria')->atLeast()->once()
            ->andReturn($repoMock);
        $repoMock->shouldReceive('all')->atLeast()->once()
            ->andReturn(new Collection([$modelMock, $modelMock, $modelMock]));

        $this->sut->persist($requestMock);
    }
}
