<?php
namespace Anavel\Crud\Tests\Abstractor\Eloquent;

use Anavel\Crud\Tests\TestBase;
use Mockery;
use Anavel\Crud\Http\Form\Generator;
use Mockery\Mock;

class GeneratorTest extends TestBase
{
    /**
     * @var Generator
     */
    protected $sut;
    /**
     * @var Mock
     */
    protected $factoryMock;

    public function setUp()
    {
        parent::setUp();

        $this->sut = new Generator($this->factoryMock = $this->mock('FormManager\FactoryInterface'));
    }

    public function tearDown()
    {
        Mockery::close();
    }

    public function test_implements_generator_interface()
    {
        $this->assertInstanceOf('Anavel\Crud\Contracts\Form\Generator', $this->sut);
    }

    public function test_sets_related_fields()
    {
        $this->factoryMock->shouldReceive('get')->andReturn($formMock = $this->mock('FormManager\ElementInterface'));
        $formMock->shouldReceive('attr', 'add');
        $relationMock = $this->mock('Anavel\Crud\Contracts\Abstractor\Relation');
        $relationMock->shouldReceive('getEditFields')
            ->atLeast()
            ->once()
            ->andReturn([]);

        $this->sut->setRelatedModelFields([$relationMock]);
    }
}
