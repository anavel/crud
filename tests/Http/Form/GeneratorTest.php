<?php
namespace Crudoado\Tests\Abstractor\Eloquent;

use Crudoado\Tests\TestBase;
use Mockery;
use ANavallaSuiza\Crudoado\Http\Form\Generator;

class GeneratorTest extends TestBase
{
    protected $generator;

    public function setUp()
    {
        parent::setUp();

        $this->generator = new Generator($this->getFactoryMock());
    }

    public function tearDown()
    {
        Mockery::close();
    }

    private function getFactoryMock()
    {
        return Mockery::mock('FormManager\FactoryInterface');
    }

    public function test_implements_generator_interface()
    {
        $this->assertInstanceOf('ANavallaSuiza\Crudoado\Contracts\Form\Generator', $this->generator);
    }
}
