<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation as RelationAbstractorContract;

class Relation implements RelationAbstractorContract
{
    protected $name;
    protected $presentation;
    protected $type;

    public function __construct()
    {

    }

    public function getName()
    {
        return $this->name;
    }

    public function getPresentation()
    {
        return $this->presentation;
    }

    public function getType()
    {
        return $this->type;
    }
}
