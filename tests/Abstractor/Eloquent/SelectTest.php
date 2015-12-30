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
        $this->fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory');

        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory', $modelFactoryMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory'));
        $modelFactoryMock->shouldReceive('getByClassName')->andReturn($this->modelAbstractorMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model'));
        $this->relationMock->shouldReceive('getRelated')->andReturn($this->relationMock);

        $this->sut = new Select(
            $config['Users']['relations']['group'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->group(),
            $this->fieldMock
        );
    }

    public function test_implements_relation_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_get_edit_fields_return_array_with_one_field()
    {
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->andReturn($dbalMock = $this->mock('\ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));
        $dbalMock->shouldReceive('getTableColumn')->andReturn($columnMock = $this->mock('Doctrine\DBAL\Schema\Column'));
        $this->modelManagerMock->shouldReceive('getRepository')->andReturn($repoMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository'));
        $repoMock->shouldReceive('all')->andReturn([]);
        $this->fieldMock->shouldReceive('setColumn', 'setConfig')->andReturn($this->fieldMock);
        $this->fieldMock->shouldReceive('get')->andReturn($field = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field'));

        $field->shouldReceive('setOptions');


        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(1, $fields);
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_throws_exception_if_display_is_not_set_in_config()
    {
        $this->setExpectedException('ANavallaSuiza\Crudoado\Abstractor\Exceptions\RelationException', 'Display should be set in config');

        $this->sut = new Select(
            $this->wrongConfig['Users']['relations']['group'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->group(),
            $this->fieldMock
        );
    }
}
