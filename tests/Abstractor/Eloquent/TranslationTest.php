<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Translation;
use Crudoado\Tests\Models\User;
use Crudoado\Tests\TestBase;
use Mockery;
use Mockery\Mock;

class TranslationTest extends TestBase
{
    /** @var  Translation */
    protected $sut;
    /** @var  Mock */
    protected $relationMock;
    /** @var  Mock */
    protected $modelManagerMock;

    public function setUp()
    {
        parent::setUp();

        $config = require __DIR__ . '/../../config.php';

        $this->relationMock = $this->mock('Illuminate\Database\Eloquent\Relations\Relation');

        $this->sut = new Translation(
            $this->modelManagerMock = Mockery::mock('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager'),
            $user = new User(),
            $user->translations(),
            $config['Users']['relations']['translations']['name'],
            $config['Users']['relations_presentation']['translations']
        );
    }

    public function test_implements_relation_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation', $this->sut);
    }

    public function test_get_edit_fields_returns_array_of_fields_with_proper_key()
    {
        $this->modelManagerMock->shouldReceive('getAbstractionLayer')
            ->andReturn($modelAbstractorMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model'));

        $modelAbstractorMock->shouldReceive('getEditFields')
            ->andReturn([$fieldMock = $this->mock('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field')]);
        $fieldMock->shouldReceive('getName')->atLeast()->once()->andReturn($fieldName = 'chompy');
        $fieldMock->shouldReceive('setName')->atLeast()->once()->with("translations[][{$fieldName}]");

        $fields = $this->sut->getEditFields();

        $this->assertInternalType('array', $fields, 'getEditFields should return an array');

        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $fields[0]);
    }
}
