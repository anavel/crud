<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\MiniCrud;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\MiniCrudPolymorphic;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Select;
use Crudoado\Tests\Models\User;
use Crudoado\Tests\TestBase;
use Mockery;
use Mockery\Mock;
use phpmock\mockery\PHPMockery;


class MiniCrudPolymorphicTest extends TestBase
{
    /** @var  MiniCrudPolymorphic */
    protected $sut;
    /** @var  Mock */
    protected $relationMock;
    /** @var  Mock */
    protected $modelManagerMock;
    /** @var  Mock */
    protected $fieldFactoryMock;

    protected $wrongConfig;
    protected $getClassMock;

    public function setUp()
    {
        parent::setUp();

        $this->wrongConfig = require __DIR__ . '/../../wrong-config.php';

        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');
        $this->fieldFactoryMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory');
        $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');

        $this->getClassMock = PHPMockery::mock('ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits',
            'get_class');
    }

    //We can not do the construct in the setup because get_class needs to be mocked differently each time
    public function buildRelation()
    {
        $config = require __DIR__ . '/../../config.php';
        $this->sut = new MiniCrudPolymorphic(
            $config['Users']['relations']['group'],
            $this->modelManagerMock,
            $user = new User(),
            $this->relationMock,
            $this->fieldFactoryMock
        );
    }

    public function test_implements_relation_interface()
    {
        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphMany');

        $this->buildRelation();
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_throws_exception_when_class_not_compatible()
    {
        $this->setExpectedException('ANavallaSuiza\Crudoado\Abstractor\Exceptions\RelationException');
        $this->getClassMock->andReturn('chompy');
        $this->buildRelation();
    }


    public function test_get_edit_fields_returns_array()
    {
        $this->relationMock->shouldReceive('getRelated', 'getPlainForeignKey', 'getPlainMorphType', 'getParent', 'getKeyName')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('getResults')->andReturn(collect([$postMock = $this->mock('Crudoado\Tests\Models\Post')]));
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->andReturn($dbalMock = $this->mock('\ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));
        $dbalMock->shouldReceive('getTableColumns')->andReturn([$columnMock = $this->mock('Doctrine\DBAL\Schema\Column')]);
        $postMock->shouldReceive('getAttribute')->andReturn('chompy');


        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')->andReturn($fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field'));
        $fieldMock->shouldReceive('setOptions');

        $fieldMock->shouldReceive('setValue')->times(1);


        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphMany');
        $this->buildRelation();
        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(2, $fields);
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }
//
//    public function test_throws_exception_if_display_is_not_set_in_config()
//    {
//        $this->setExpectedException('ANavallaSuiza\Crudoado\Abstractor\Exceptions\RelationException', 'Display should be set in config');
//
//        $this->sut = new Select(
//            $this->wrongConfig['Users']['relations']['group'],
//            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
//            $user = new User(),
//            $user->group(),
//            $this->fieldMock
//        );
//    }
}
