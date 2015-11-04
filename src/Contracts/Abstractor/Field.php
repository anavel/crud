<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface Field
{
    public function name();

    public function presentation();

    public function type();

    public function setCustomFormType($formType);

    public function hasCustomFormType();

    public function getCustomFormType();

    public function setValidationRules($rules);

    public function getValidationRules();

    public function setFunctions($functions);

    public function applyFunctions($value);
}
