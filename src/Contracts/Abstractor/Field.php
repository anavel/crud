<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface Field
{
    public function name();

    public function presentation();

    public function type();
}
