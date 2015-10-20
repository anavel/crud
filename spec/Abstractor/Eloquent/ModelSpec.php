<?php

namespace spec\ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;

class ModelSpec extends ObjectBehavior
{
    public function let(ModelManager $modelManager)
    {
        $this->beConstructedWith(array(), $modelManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model');
    }

    public function it_implements_model_interface()
    {
        $this->shouldImplement('ANavallaSuiza\Crudoado\Contracts\Abstractor\Model');
    }
}
