<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field as FieldAbstractorContract;

class Field implements FieldAbstractorContract
{
    protected $name;
    protected $type;

    public function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    public function name()
    {
        return $this->name;
    }

    public function presentation()
    {
        return $this->name;
    }

    public function type()
    {
        return $this->type;
    }
}

