<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Translation;
use Crudoado\Tests\Models\User;
use Crudoado\Tests\TestBase;
use Mockery;
use Mockery\Mock;
use Illuminate\Database\Eloquent\Model as LaravelModel;


class TranslationTest extends TestBase
{
    /** @var  Translation */
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

        $this->sut = new Translation(
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->translations(),
            $config['Users']['relations']['translations']['name'],
            $config['Users']['relations_presentation']['translations']
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

        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory', $relationFactoryMock);
        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory', $modelFactoryMock);

        $modelFactoryMock->shouldReceive('getByClassName')
            ->andReturn($this->modelManagerMock);

        $this->modelManagerMock->shouldReceive('getEditFields')->atLeast()->once()
            ->andReturn([$fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field')]);

        $fieldMock->shouldReceive('getName')->atLeast()->once()->andReturn($fieldName = 'chompy');
        $fieldMock->shouldReceive('setName')->atLeast()->once()->with(matchesPattern("/^translations\[[\d]\]\[chompy\]/"));

        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_persist()
    {
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('input')->with('translations')->atLeast()->once();

        $this->sut->persist($requestMock);
    }
}
