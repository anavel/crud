<?php
namespace ANavallaSuiza\Crudoado\Contracts\Form;

interface Generator
{
    public function setModelFields(array $fields);

    public function getForm($action);
}
