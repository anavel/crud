<?php

namespace spec\ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type as DbalType;

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

    public function it_returns_name()
    {
        $this->name()->shouldReturn('username');
    }

    public function it_returns_presentation()
    {
        $this->presentation()->shouldReturn('User name');
    }

    public function it_generates_presentation_if_empty(Column $column)
    {
        $this->beConstructedWith($column, 'user_name');

        $this->presentation()->shouldReturn('User name');
    }

    public function it_returns_type(Column $column)
    {
        $column->getType()->willReturn(DbalType::INTEGER);

        $this->beConstructedWith($column, 'user_name');

        $this->type()->shouldReturn(DbalType::INTEGER);
    }

    public function it_returns_validation_rules_as_string(Column $column)
    {
        $column->getNotNull()->willReturn(false);
        $column->getNotnull()->willReturn(false);

        $this->beConstructedWith($column, 'user_name');

        $this->getValidationRules()->shouldBeString();
    }
}
