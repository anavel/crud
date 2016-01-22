<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\ModelFactory;
use Mockery\Mock;


class ModelFactoryTest extends TestBase
{
    /** @var  ModelFactory */
    protected $sut;

    /** @var Mock */
    protected $modelManagerMock;
    /** @var Mock */
    protected $relationMock;
    /** @var Mock */
    protected $fieldMock;
    /** @var Mock */
    protected $generatorMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->modelManagerMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
        $this->relationMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory');
        $this->fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory');
        $this->generatorMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Form\Generator');

        $this->sut = new ModelFactory($config, $this->modelManagerMock, $this->relationMock, $this->fieldMock, $this->generatorMock);
    }

    public function test_implements_model__factory_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory', $this->sut);
    }

    public function test_throws_exception_when_model_not_found()
    {
        $this->setExpectedException('Exception', "Model chompy not found on configuration" );

        $model = $this->sut->getBySlug('chompy');
    }

    public function test_gets_model_by_slug()
    {
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->once()->andReturn($this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));

        $this->modelManagerMock->shouldReceive('getModelInstance')->once();

        $model = $this->sut->getBySlug('users');

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model', $model);
    }

    public function test_gets_model_by_slug_and_id()
    {
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->once()->andReturn($this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));

        $this->modelManagerMock->shouldReceive('getRepository')->once()->andReturn($repositoryMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository'));

        $repositoryMock->shouldReceive('findByOrFail', 'getModel', 'getKeyName')->once()->andReturn($repositoryMock);

        $model = $this->sut->getBySlug('users', 1);

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model', $model);
    }

    public function test_gets_model_by_classname()
    {
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->once()->andReturn($this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));
        $this->modelManagerMock->shouldReceive('getModelInstance')->once()->with('Crudoado\Tests\Models\User');

        $model = $this->sut->getByClassName('Crudoado\Tests\Models\User', []);

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model', $model);
    }
}
