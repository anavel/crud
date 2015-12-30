<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\MiniCrud;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Select;
use Crudoado\Tests\Models\User;
use Crudoado\Tests\TestBase;
use Mockery;
use Mockery\Mock;
use phpmock\mockery\PHPMockery;


class MiniCrudTest extends TestBase
{
    /** @var  MiniCrud() */
    protected $sut;
    /** @var  Mock */
    protected $relationMock;
    /** @var  Mock */
    protected $modelManagerMock;
    /** @var  Mock */
    protected $fieldFactoryMock;
    /** @var  Mock */
    protected $modelAbstractorMock;

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

        \App::instance('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory', $modelFactoryMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory'));
        $modelFactoryMock->shouldReceive('getByClassName')->andReturn($this->modelAbstractorMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model'));
        $this->relationMock->shouldReceive('getRelated')->andReturn($this->relationMock);
    }

    //We can not do the construct in the setup because get_class needs to be mocked differently each time
    public function buildRelation()
    {
        $config = require __DIR__ . '/../../config.php';
        $this->sut = new MiniCrud(
            $config['Users']['relations']['group'],
            $this->modelManagerMock,
            $user = new User(),
            $this->relationMock,
            $this->fieldFactoryMock
        );
    }

    public function test_implements_relation_interface()
    {
        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\HasMany');

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
        $this->relationMock->shouldReceive('getRelated', 'getPlainForeignKey', 'getParent',
            'getKeyName')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('getResults')->andReturn(collect([$postMock = $this->mock('Crudoado\Tests\Models\Post')]));
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->andReturn($dbalMock = $this->mock('\ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));
        $dbalMock->shouldReceive('getTableColumns')->andReturn([$columnMock = $this->mock('Doctrine\DBAL\Schema\Column')]);
        $postMock->shouldReceive('getAttribute')->andReturn('chompy');


        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')->andReturn($fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field'));
        $fieldMock->shouldReceive('setOptions');

        $fieldMock->shouldReceive('setValue')->times(1);

        $this->modelAbstractorMock->shouldReceive('getRelations')->times(1)->andReturn([$secondaryRelationMock = $this->mock('ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Select')]);
        $secondaryRelationMock->shouldReceive('getEditFields')->andReturn([]);


        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\HasMany');
        $this->buildRelation();
        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(2, $fields);
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }

    public function test_persist_with_no_old_results()
    {
        $inputArray = [
            '0' => [
                'field' => 1,
                'otherField' => 3,
                'someOtherField' => 3,
            ]
        ];
        $requestMock = $this->mock('Illuminate\Http\Request');
//
        $requestMock->shouldReceive('input')->with('group')->atLeast()->once()->andReturn($inputArray);

        $this->relationMock->shouldReceive('getForeignKey');
        $this->relationMock->shouldReceive('getRelated', 'getParent', 'get')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('keyBy')->once()->andReturn(collect());
        $this->relationMock->shouldReceive('getKeyName')->andReturn('id');
        $this->relationMock->shouldReceive('newInstance')->andReturn($modelMock = $this->mock('Crudoado\Tests\Models\Post'));

        $modelMock->shouldReceive('getKey')->andReturn(1);
        $modelMock->shouldReceive('setAttribute')->times(4);
        $modelMock->shouldReceive('save')->times(1);

        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\HasMany');
        $this->buildRelation();

        $fields = $this->sut->persist($requestMock);
    }

    public function test_persist_with_old_results()
    {
        $inputArray = [
            '0' => [
                'id' => 1,
                'otherField' => 3,
                'someOtherField' => 3,
            ],
            '1' => [
                'id' => 1,
                'otherField' => 3,
                'someOtherField' => 3,
            ]
        ];
        $requestMock = $this->mock('Illuminate\Http\Request');
//
        $requestMock->shouldReceive('input')->with('group')->atLeast()->once()->andReturn($inputArray);

        $this->relationMock->shouldReceive('getForeignKey');
        $this->relationMock->shouldReceive('getRelated', 'getParent', 'get')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('newInstance');
        $this->relationMock->shouldReceive('keyBy')->once()->andReturn(collect([1 => $modelMock = $this->mock('Crudoado\Tests\Models\Post')]));
        $this->relationMock->shouldReceive('getKeyName')->andReturn('id');

        $modelMock->shouldReceive('getKey')->andReturn(1);
        $modelMock->shouldReceive('setAttribute')->times(8);
        $modelMock->shouldReceive('save')->times(2);

        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\HasMany');
        $this->buildRelation();

        $fields = $this->sut->persist($requestMock);
    }
}
