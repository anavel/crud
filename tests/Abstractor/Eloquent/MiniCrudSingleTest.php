<?php
namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Relation\MiniCrudSingle;
use Anavel\Crud\Tests\Models\User;
use Anavel\Crud\Tests\TestBase;
use Mockery;
use Mockery\Mock;
use phpmock\mockery\PHPMockery;


class MiniCrudSingleTest extends TestBase
{
    /** @var  MiniCrudSingle */
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
        $this->fieldFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');
        $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');

        $this->getClassMock = PHPMockery::mock('Anavel\Crud\Abstractor\Eloquent\Relation\Traits',
            'get_class');

        \App::instance('Anavel\Crud\Contracts\Abstractor\ModelFactory', $modelFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\ModelFactory'));
        $modelFactoryMock->shouldReceive('getByClassName')->andReturn($this->modelAbstractorMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Model'));
        $this->relationMock->shouldReceive('getRelated')->andReturn($this->relationMock);
    }

    //We can not do the construct in the setup because get_class needs to be mocked differently each time
    public function buildRelation()
    {
        $config = require __DIR__ . '/../../config.php';
        $this->sut = new MiniCrudSingle(
            $config['Users']['relations']['group'],
            $this->modelManagerMock,
            $user = new User(),
            $this->relationMock,
            $this->fieldFactoryMock
        );
    }

    public function test_implements_relation_interface()
    {
        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphOne');

        $this->buildRelation();
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Relation', $this->sut);
    }

//
    public function test_throws_exception_when_class_not_compatible()
    {
        $this->setExpectedException('Anavel\Crud\Abstractor\Exceptions\RelationException');
        $this->getClassMock->andReturn('chompy');
        $this->buildRelation();
    }


    public function test_get_edit_fields_returns_array()
    {
        $this->relationMock->shouldReceive('getRelated', 'getPlainForeignKey', 'getPlainMorphType', 'getParent',
            'getKeyName')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('getResults')->andReturn($postMock = $this->mock('Anavel\Crud\Tests\Models\Post'));
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->andReturn($dbalMock = $this->mock('\ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));
        $dbalMock->shouldReceive('getTableColumns')->andReturn([
            $columnMock = $this->mock('Doctrine\DBAL\Schema\Column'),
            $columnMock = $this->mock('Doctrine\DBAL\Schema\Column')
        ]);
        $postMock->shouldReceive('hasGetMutator')->andReturn('false');
        $postMock->shouldReceive('getAttributeValue')->andReturn('1');
        $postMock->shouldReceive('getAttribute')->andReturn('chompy');


        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')->andReturn($fieldMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Field'));
        $fieldMock->shouldReceive('setOptions');

        $fieldMock->shouldReceive('setValue')->times(2);

        $this->modelAbstractorMock->shouldReceive('getRelations')->times(1)->andReturn([$secondaryRelationMock = $this->mock('Anavel\Crud\Abstractor\Eloquent\Relation\Select')]);
        $secondaryRelationMock->shouldReceive('getEditFields')->andReturn([]);


        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphOne');
        $this->buildRelation();
        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(1, $fields);
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['group'][0]);
    }

    public function test_persist_with_no_old_results()
    {
        $inputArray = [
            'field'          => 1,
            'otherField'     => 3,
            'someOtherField' => 3,
        ];
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('input')->with('group')->atLeast()->once()->andReturn($inputArray);

        $this->relationMock->shouldReceive('getForeignKey', 'getPlainMorphType', 'getMorphClass');
        $this->relationMock->shouldReceive('getRelated', 'getParent')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('getResults');
        $this->relationMock->shouldReceive('newInstance')->andReturn($modelMock = $this->mock('Anavel\Crud\Tests\Models\Post'));

        $modelMock->shouldReceive('getKey')->andReturn(1);
        $modelMock->shouldReceive('setAttribute')->times(5);
        $modelMock->shouldReceive('save')->times(1);

        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphOne');
        $this->buildRelation();

        $fields = $this->sut->persist($requestMock);
    }

    public function test_persist_with_old_results()
    {
        $inputArray = [
            'id'             => 1,
            'otherField'     => 3,
            'someOtherField' => 3,
        ];
        $requestMock = $this->mock('Illuminate\Http\Request');

        $requestMock->shouldReceive('input')->with('group')->atLeast()->once()->andReturn($inputArray);
        $this->relationMock->shouldReceive('getForeignKey', 'getPlainMorphType', 'getMorphClass');
        $this->relationMock->shouldReceive('getRelated', 'getParent')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('getResults')->andReturn($modelMock = $this->mock('Anavel\Crud\Tests\Models\Post'));
        $this->relationMock->shouldReceive('newInstance')->never();

        $modelMock->shouldReceive('getKey')->andReturn(1);
        $modelMock->shouldReceive('setAttribute')->times(5);
        $modelMock->shouldReceive('save')->times(1);

        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphOne');
        $this->buildRelation();

        $fields = $this->sut->persist($requestMock);
    }
}
