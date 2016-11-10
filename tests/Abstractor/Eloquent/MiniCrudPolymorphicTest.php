<?php

namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Relation\MiniCrudPolymorphic;
use Anavel\Crud\Tests\Models\User;
use Anavel\Crud\Tests\TestBase;
use Mockery;
use Mockery\Mock;
use phpmock\mockery\PHPMockery;

class MiniCrudPolymorphicTest extends TestBase
{
    /** @var MiniCrudPolymorphic */
    protected $sut;
    /** @var Mock */
    protected $relationMock;
    /** @var Mock */
    protected $modelManagerMock;
    /** @var Mock */
    protected $fieldFactoryMock;
    /** @var Mock */
    protected $modelAbstractorMock;
    /** @var Mock */
    protected $dbalMock;
    /** @var Mock */
    protected $requestMock;

    protected $wrongConfig;
    protected $getClassMock;

    public function setUp()
    {
        parent::setUp();

        $this->wrongConfig = require __DIR__.'/../../wrong-config.php';

        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');
        $this->fieldFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');
        $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
        $this->requestMock = $this->mock('Illuminate\Http\Request');

        $this->modelManagerMock->shouldReceive('getAbstractionLayer')->andReturn($this->dbalMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer'));

        $this->getClassMock = PHPMockery::mock('Anavel\Crud\Abstractor\Eloquent\Relation\Traits',
            'get_class');

        \App::instance('Anavel\Crud\Contracts\Abstractor\ModelFactory', $modelFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\ModelFactory'));
        $modelFactoryMock->shouldReceive('getByClassName')->andReturn($this->modelAbstractorMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Model'));
        $this->relationMock->shouldReceive('getRelated')->andReturn($this->relationMock);
    }

    public function buildRelation()
    {
        $config = require __DIR__.'/../../config.php';
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
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_throws_exception_when_class_not_compatible()
    {
        $this->setExpectedException('Anavel\Crud\Abstractor\Exceptions\RelationException');
        $this->getClassMock->andReturn('chompy');
        $this->buildRelation();
    }

    public function test_get_edit_fields_returns_array()
    {
        $this->modelAbstractorMock->shouldReceive('getColumns')->times(1)->andReturn(['columname' => $columnMock = $this->mock('Doctrine\DBAL\Schema\Column'), '__delete' => $columnMock = $this->mock('Doctrine\DBAL\Schema\Column')]);

        $this->relationMock->shouldReceive('getResults')->andReturn(collect([$postMock = $this->mock('Anavel\Crud\Tests\Models\Post')]));

        $this->relationMock->shouldReceive('getRelated', 'getPlainForeignKey', 'getPlainMorphType', 'getParent',
            'getKeyName')->andReturn($this->relationMock);

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')->andReturn($fieldMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Field'));

        $this->relationMock->shouldReceive('newInstance')->andReturn($postMock);

        $this->modelAbstractorMock->shouldReceive('setInstance')->with($postMock);

        $postMock->shouldReceive('getAttribute')->andReturn('idValue');


        $fieldMock->shouldReceive('setOptions');

        $fieldMock->shouldReceive('setValue')->times(1);

        $this->modelAbstractorMock->shouldReceive('getRelations')->times(2)->andReturn([$this->secondaryRelationMock = $this->mock('Anavel\Crud\Abstractor\Eloquent\Relation\Select')]);
        $this->secondaryRelationMock->shouldReceive('getEditFields')->andReturn([]);



        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphMany');
        $this->buildRelation();
        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(1, $fields);
        $this->assertArrayHasKey('group', $fields);
        $this->assertInternalType('array', $fields['group']);
        $this->assertCount(2, $fields['group']); //One for the old result, one for the new one
        $this->assertInternalType('array', $fields['group'][0]);
        $this->assertCount(1, $fields['group'][0]); // Only the mocked field
        $this->assertArrayHasKey('columname', $fields['group'][0]);
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['group'][0]['columname']);

        $this->assertArrayHasKey('idValue', $fields['group']);
        $this->assertInternalType('array', $fields['group']['idValue']);
        $this->assertCount(2, $fields['group']['idValue']); // Mocked field, plus delete checkbox
        $this->assertArrayHasKey('columname', $fields['group']['idValue']);
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $fields['group']['idValue']['columname']);
    }

    public function test_persist_with_no_old_results()
    {
        $inputArray = [
            '0' => [
                'field'          => 1,
                'otherField'     => 3,
                'someOtherField' => 3,
            ],
        ];

        $this->relationMock->shouldReceive('getForeignKey', 'getPlainMorphType', 'getMorphClass');
        $this->relationMock->shouldReceive('getRelated', 'getParent', 'getResults')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('keyBy')->once()->andReturn(collect());
        $this->relationMock->shouldReceive('getKeyName')->andReturn('id');
        $this->relationMock->shouldReceive('newInstance')->andReturn($modelMock = $this->mock('Anavel\Crud\Tests\Models\Post'));
        $this->modelAbstractorMock->shouldReceive('getRelations')->andReturn(collect());

        $this->modelAbstractorMock->shouldReceive('setInstance')->with($modelMock);

        // This, basically, re-tests getEditFields... It shouldn't be re-tested, but I can't figure out how to partially mock that method
        $this->relationMock->shouldReceive('getRelated', 'getPlainForeignKey', 'getParent',
            'getKeyName')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('getResults')->andReturn(collect());
        $this->modelAbstractorMock->shouldReceive('getColumns')->times(1)->andReturn([$columnMock = $this->mock('Doctrine\DBAL\Schema\Column')]);
        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')->andReturn($fieldMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Field'));
        ////////

        $fieldMock->shouldReceive('getName');
        $fieldMock->shouldReceive('getFormField');
        $modelMock->shouldReceive('getKey')->andReturn(1);
        $modelMock->shouldReceive('setAttribute')->times(5);
        $modelMock->shouldReceive('save')->times(1);

        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphMany');
        $this->buildRelation();

        $fields = $this->sut->persist($inputArray, $this->requestMock);
    }

    public function test_persist_with_old_results()
    {
        $this->markTestSkipped();
        $inputArray = [
            '0' => [
                'id'             => 1,
                'otherField'     => 3,
                'someOtherField' => 3,
            ],
            '1' => [
                'id'             => 1,
                'otherField'     => 3,
                'someOtherField' => 3,
            ],
        ];

        $this->relationMock->shouldReceive('getForeignKey', 'getPlainMorphType', 'getMorphClass');
        $this->relationMock->shouldReceive('getRelated', 'getParent', 'getResults')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('newInstance');
        $this->relationMock->shouldReceive('keyBy')->once()->andReturn(collect([1 => $modelMock = $this->mock('Anavel\Crud\Tests\Models\Post')]));
        $this->relationMock->shouldReceive('getKeyName')->andReturn('id');
        $this->modelAbstractorMock->shouldReceive('getRelations')->andReturn(collect());

        $this->modelAbstractorMock->shouldReceive('setInstance')->with($modelMock);


        $modelMock->shouldReceive('getKey')->andReturn(1);
        $modelMock->shouldReceive('setAttribute')->times(10);
        $modelMock->shouldReceive('save')->times(2);

        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\MorphMany');
        $this->buildRelation();

        $fields = $this->sut->persist($inputArray, $this->requestMock);
    }
}
