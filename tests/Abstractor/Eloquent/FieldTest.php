<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use Mockery;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field;

class FieldTest extends TestBase
{
    protected $field;

    public function setUp()
    {
        parent::setUp();

        $this->field = new Field($this->getColumnMock(), 'username', 'User name');
    }

    public function tearDown()
    {
        Mockery::close();
    }

    private function getColumnMock()
    {
        return Mockery::mock('Doctrine\DBAL\Schema\Column');
    }

    public function test_implements_field_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field', $this->field);
    }

    public function test_returns_name()
    {

    }

    public function test_returns_presentation()
    {

    }

    public function test_generates_presentation_if_empty()
    {

    }

    public function test_returns_type()
    {

    }

    public function test_returns_validation_rules_as_string()
    {

    }
}
