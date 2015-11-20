<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use Mockery;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field;
use Mockery\Mock;

class FieldTest extends TestBase
{
    /** @var  Field */
    protected $sut;
    /** @var Mock */
    protected $columnMock;
    /** @var Mock */
    protected $elementMock;

    public function setUp()
    {
        parent::setUp();

        $this->columnMock = $this->mock('Doctrine\DBAL\Schema\Column');
        $this->elementMock = $this->mock('FormManager\Elements\Element');

        $this->sut = new Field($this->columnMock, $this->elementMock, 'user_name', 'User name');
    }

    public function test_implements_field_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $this->sut);
    }

    public function test_generates_presentation_if_empty()
    {
        $presentation = $this->sut->presentation();

        $this->assertEquals('User name', $presentation, 'Presentation returned incorrectly');
    }

    public function test_returns_type()
    {
        $this->columnMock->shouldReceive('getType')->andReturn($this->mock('Doctrine\DBAL\Types\Type'));

        $type = $this->sut->type();


        $this->assertInstanceOf('Doctrine\DBAL\Types\Type', $type);
    }

    public function test_returns_required_when_validation_empty_and_dbal_not_null()
    {
        $this->columnMock->shouldReceive('getNotnull')->andReturn(true);

        $rules = $this->sut->getValidationRules();

        $this->assertEquals('required', $rules, 'Required rule was not set');
    }
}
