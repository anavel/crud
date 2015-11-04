<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field as FieldAbstractorContract;
use Doctrine\DBAL\Schema\Column;

class Field implements FieldAbstractorContract
{
    protected $dbal;
    protected $name;
    protected $presentation;
    protected $customFormType;
    protected $validationRules;
    protected $functions;

    public function __construct(Column $column, $name, $presentation = null)
    {
        $this->dbal = $column;
        $this->name = $name;
        $this->presentation = $presentation;
        $this->customFormType = null;
        $this->validationRules = array();
        $this->functions = array();
    }

    public function name()
    {
        return $this->name;
    }

    public function presentation()
    {
        return $this->presentation ? : ucfirst(str_replace('_', ' ', $this->name));
    }

    public function type()
    {
        return $this->dbal->getType();
    }

    public function setCustomFormType($formType)
    {
        $this->customFormType = $formType;
    }

    public function hasCustomFormType()
    {
        return isset($this->customFormType);
    }

    public function getCustomFormType()
    {
        return $this->customFormType;
    }

    public function setValidationRules($rules)
    {
        $this->validationRules = explode('|', $rules);
    }

    public function getValidationRules()
    {
        if (count($this->validationRules) === 0) {
            if ($this->dbal->getNotNull()) {
                $this->validationRules[] = 'required';
            }
        }

        return implode('|', $this->validationRules);
    }

    public function setFunctions($functions)
    {
        if (! is_array($functions)) {
            $functions = array($functions);
        }

        $this->functions = $functions;
    }

    public function applyFunctions($value)
    {
        return $value;
    }
}
