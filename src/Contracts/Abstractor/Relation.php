<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface Relation
{
    public function name();

    public function presentation();

    public function type();
}
