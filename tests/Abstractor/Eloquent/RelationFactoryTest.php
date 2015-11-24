<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\Models\User;
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
    /** @var Mock */
    protected $fieldMock;

    public function setUp()
    {
        parent::setUp();

        $this->modelManagerMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
        $this->fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory');

        $this->sut = new RelationFactory($this->modelManagerMock, $this->fieldMock);

        $this->sut->setModel(new User());
    }

    public function test_implements_relation_factory_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory', $this->sut);
    }

    public function test_throws_exception_when_relation_not_supported()
    {
        $config = require __DIR__ . '/../../wrong-config.php';
        $this->sut->setConfig($config['Users']['relations']['translations']);
        $this->setExpectedException('ANavallaSuiza\Crudoado\Abstractor\Exceptions\FactoryException', 'Unexpected relation type: fake');

        $relation = $this->sut->get('translations');
    }

    public function test_throws_exception_when_relation_does_not_exist()
    {
        $this->setExpectedException('Exception', 'Relation chompy does not exist on');

        $model = $this->sut->get('chompy');
    }
}
