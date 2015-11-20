<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field as FieldAbstractorContract;
use Doctrine\DBAL\Schema\Column;

class Field implements FieldAbstractorContract
{
    protected $dbal;
    protected $formElement;
    protected $name;
    protected $value;
    protected $presentation;
    protected $customFormType;
    protected $validationRules;
    protected $functions;
    protected $options;

    public function __construct(Column $column, $name, $presentation = null)
    {
        $this->dbal = $column;
        $this->name = $name;
        $this->presentation = $presentation;
        $this->customFormType = null;
        $this->validationRules = array();
        $this->functions = array();
        $this->options = [];
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
            if ($this->dbal->getNotnull()) {
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
        foreach ($this->functions as $function) {
            if (! function_exists($function)) {
                throw new \Exception("Function ".$function." does not exist");
            }

            $value = call_user_func($function, $value);
        }

        return $value;
    }

    /**
     * @param string $value
     * @return void
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
