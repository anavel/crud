<?php
namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Tests\Models\User;
use Anavel\Crud\Tests\TestBase;
use Anavel\Crud\Abstractor\Eloquent\RelationFactory;
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
        $this->fieldMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');

        $this->sut = new RelationFactory($this->modelManagerMock, $this->fieldMock);

        $this->sut->setModel(new User());
    }

    public function test_implements_relation_factory_interface()
    {
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\RelationFactory', $this->sut);
    }

    public function test_throws_exception_when_relation_not_supported()
    {
        $config = require __DIR__ . '/../../wrong-config.php';
        $this->sut->setConfig($config['Users']['relations']['translations']);
        $this->setExpectedException('Anavel\Crud\Abstractor\Exceptions\FactoryException', 'Unexpected relation type: fake');

        $relation = $this->sut->get('translations');
    }

    public function test_throws_exception_when_relation_does_not_exist()
    {
        $this->setExpectedException('Exception', 'Relation chompy does not exist on');

        $model = $this->sut->get('chompy');
    }
}
