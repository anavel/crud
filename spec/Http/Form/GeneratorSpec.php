<?php

namespace spec\ANavallaSuiza\Crudoado\Http\Form;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use FormManager\FactoryInterface;

class GeneratorSpec extends ObjectBehavior
{
    public function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ANavallaSuiza\Crudoado\Http\Form\Generator');
    }

    function it_implements_generator_interface()
    {
        $this->shouldImplement('ANavallaSuiza\Crudoado\Contracts\Form\Generator');
    }
}
