<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\RelationFactory;
use Mockery\Mock;


class RelationFactoryTest extends TestBase
{
    /** @var  RelationFactory */
    protected $sut;

    /** @var Mock */
    protected $modelManagerMock;
    /** @var Mock */
    protected $userMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->modelManagerMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
        $this->userMock = $this->mock('\Crudoado\Tests\Models\User');

        $this->sut = new RelationFactory($this->modelManagerMock);

        $this->sut->setModel($this->userMock);
    }

    public function test_implements_relation_factory_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory', $this->sut);
    }

    public function test_get_relation()
    {
        //Hai que usar isto para que funcionen as funcións nativas de php https://github.com/php-mock/php-mock-mockery

//        $this->userMock->shouldReceive('translations')->andReturn(\App::make('\Illuminate\Database\Eloquent\Relations\HasMany'));
//        $relation = $this->stu->get('translations');
    }
//
    public function test_throws_exception_when_relation_does_not_exist()
    {
        $this->setExpectedException('Exception', 'Relation chompy does not exist on');

        $model = $this->sut->get('chompy');
    }
}
