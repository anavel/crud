<?php

namespace spec\ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\DBAL\Schema\Column;

class FieldSpec extends ObjectBehavior
{
    public function let(Column $column)
    {
        $this->beConstructedWith($column, 'username', 'User name');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field');
    }

    public function it_implements_field_interface()
    {
        $this->shouldImplement('ANavallaSuiza\Crudoado\Contracts\Abstractor\Field');
    }
}
