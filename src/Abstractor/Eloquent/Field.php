<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field as FieldAbstractorContract;
use Doctrine\DBAL\Types\Type;

class Field implements FieldAbstractorContract
{
    protected $type;
    protected $name;
    protected $presentation;

    public function __construct(Type $type, $name, $presentation = null)
    {
        $this->type = $type;
        $this->name = $name;
        $this->presentation = $presentation;
    }

    public function name()
    {
        return $this->name;
    }

    public function presentation()
    {
        return $this->presentation ? : $this->name;
    }

    public function type()
    {
        return $this->type;
    }
}
