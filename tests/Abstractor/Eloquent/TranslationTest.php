<?php
namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Relation\Translation;
use Anavel\Crud\Tests\Models\User;
use Anavel\Crud\Tests\Models\UserTranslations;
use Anavel\Crud\Tests\TestBase;
use Mockery;
use Mockery\Mock;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use phpmock\mockery\PHPMockery;


class TranslationTest extends TestBase
{
    /** @var  Translation */
    protected $sut;
    /** @var  Mock */
    protected $relationMock;
    /** @var  Mock */
    protected $modelManagerMock;
    /** @var  Mock */
    protected $fieldFactoryMock;
    /** @var  Mock */
    protected $modelAbstractorMock;
    /** @var  Mock */
    protected $requestMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');
        $this->fieldFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\FieldFactory');
        $this->requestMock = $this->mock('Illuminate\Http\Request');

        \App::instance('Anavel\Crud\Contracts\Abstractor\ModelFactory', $modelFactoryMock = $this->mock('Anavel\Crud\Contracts\Abstractor\ModelFactory'));
        $modelFactoryMock->shouldReceive('getByClassName')->andReturn($this->modelAbstractorMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Model'));
        $this->relationMock->shouldReceive('getRelated')->andReturn($this->relationMock);

        $this->getClassMock = PHPMockery::mock('Anavel\Crud\Abstractor\Eloquent\Relation\Traits',
            'get_class');
        $this->getClassMock->andReturn('Illuminate\Database\Eloquent\Relations\HasMany');

        $this->sut = new Translation(
            $config['Users']['relations']['translations'],
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $this->relationMock,
            $this->fieldFactoryMock
        );
    }

    public function test_implements_relation_interface()
    {
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_get_edit_fields_returns_array_of_fields_with_proper_key()
    {
        $dbalMock = $this->mock('ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer');

        $this->fieldFactoryMock->shouldReceive('setColumn', 'setConfig')
            ->andReturn($this->fieldFactoryMock);
        $this->fieldFactoryMock->shouldReceive('get')
            ->andReturn($fieldMock = $this->mock('Anavel\Crud\Abstractor\Eloquent\Field'));

        $fieldMock->shouldReceive('setValue');

        $this->relationMock->shouldReceive('getResults')->andReturn(collect());
        $this->relationMock->shouldReceive('getPlainForeignKey', 'getKeyName')->andReturn('id');
        $this->relationMock->shouldReceive('getParent')->andReturn($this->relationMock);

        $columnMock = $this->mock('Doctrine\DBAL\Schema\Column');

        $this->modelManagerMock->shouldReceive('getAbstractionLayer')
            ->andReturn($dbalMock);

        $columnMock = $this->mock('Doctrine\DBAL\Schema\Column');

        $dbalMock->shouldReceive('getTableColumns')
            ->once()
            ->andReturn([
                'id'      => $columnMock,
                'user_id' => $columnMock,
                'locale'  => $columnMock,
                'bio'     => $columnMock
            ]);

        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');
        $this->assertCount(1, $fields);
        $this->assertInternalType('array', $fields['translations'], 'getEditFields should return an array with a "translations" index');
        $this->assertArrayHasKey('gl', $fields['translations']);
        $this->assertArrayHasKey('es', $fields['translations']);
        $this->assertArrayHasKey('en', $fields['translations']);

        $this->assertInternalType('array', $fields['translations']['gl']);
        foreach($fields['translations']['gl'] as $element) {
            $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $element);
        }
    }

    public function test_persist_with_no_results()
    {
        $inputArray = [
            'es' => [
                'locale' => 'es',
                'otherField' => 'something',
            ],
            'en' => [
                'locale' => 'en',
                'otherField' => '',
            ]
        ];

        $this->relationMock->shouldReceive('getResults')->andReturn(collect());
        $this->relationMock->shouldReceive('getForeignKey')->andReturn('id');
        $this->relationMock->shouldReceive('newInstance')->andReturn($modelMock = $this->mock(UserTranslations::class));

        $modelMock->shouldReceive('setAttribute')->times(3); // Should pass the language with only locale
        $modelMock->shouldReceive('save')->once();


        $this->sut->persist($inputArray, $this->requestMock);
    }

    public function test_persist_with_old_results()
    {
        $inputArray = [
            'es' => [
                'locale' => 'es',
                'otherField' => 'something',
            ],
            'en' => [
                'locale' => 'en',
                'otherField' => '',
            ]
        ];

        $modelMock = $this->mock(UserTranslations::class);
        $this->relationMock->shouldReceive('getResults')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('keyBy')->andReturn(collect(['es' => $modelMock, 'en' => $modelMock]));
        $this->relationMock->shouldReceive('getResults')->andReturn($this->relationMock);
        $this->relationMock->shouldReceive('getForeignKey')->andReturn('id');
        $this->relationMock->shouldReceive('newInstance')->andReturn();

        $modelMock->shouldReceive('setAttribute')->times(3); // Should pass the language with only locale
        $modelMock->shouldReceive('delete')->once(); // Should delete the already existing language, since it's empty
        $modelMock->shouldReceive('save')->once();


        $this->sut->persist($inputArray, $this->requestMock);
    }
}
