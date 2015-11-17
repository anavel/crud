<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Select;
use Crudoado\Tests\Models\User;
use Crudoado\Tests\TestBase;
use Mockery;
use Mockery\Mock;


class SelectTest extends TestBase
{
    /** @var  Select */
    protected $sut;
    /** @var  Mock */
    protected $relationMock;
    /** @var  Mock */
    protected $modelManagerMock;

    public function setUp()
    {
        parent::setUp();
//
//        $config = require __DIR__ . '/../../config.php';
//
//        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');
//
//        $this->sut = new Select(
//            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
//            $user = new User(),
//            $user->group(),
//            'group',
//            'Group'
//        );
    }

    public function test_implements_relation_interface()
    {
//        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_get_edit_fields_return_array_with_one_field()
    {
//        $relationFactoryMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory');
//        $modelFactoryMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory');
//
//        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory', $relationFactoryMock);
//        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory', $modelFactoryMock);
//
//        $modelFactoryMock->shouldReceive('getByClassName')
//            ->andReturn($this->modelManagerMock);
//
//        $fields = $this->sut->getEditFields();
//
//        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
//
//        $this->assertCount(1, $fields);
//
//        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }
}
