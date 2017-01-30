<?php

namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Field;
use Anavel\Crud\Tests\TestBase;
use Mockery\Mock;

class FieldTest extends TestBase
{
    /** @var Field */
    protected $sut;
    /** @var Mock */
    protected $columnMock;
    /** @var Mock */
    protected $formFieldMock;

    public function setUp()
    {
        parent::setUp();

        $this->columnMock = $this->mock('Doctrine\DBAL\Schema\Column');
        $this->formFieldMock = $this->mock('FormManager\Fields\Field');

        $this->sut = new Field($this->columnMock, $this->formFieldMock, 'user_name', 'User name');
    }

    public function test_implements_field_interface()
    {
        $this->assertInstanceOf('Anavel\Crud\Contracts\Abstractor\Field', $this->sut);
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
