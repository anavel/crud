<?php
namespace ANavallaSuiza\Crudoado\Contracts\Form;

interface Generator
{
    public function setModel($model);

    public function setModelFields(array $fields);

    public function getForm($action);

    public function getValidationRules();
}
