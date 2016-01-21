<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface Field
{
    public function getName();

    /**
     * @param string $name
     */
    public function setName($name);

    public function presentation();

    public function type();

    public function setValidationRules($rules);

    public function getValidationRules();

    public function setFunctions($functions);

    public function applyFunctions($value);

    /**
     * @param string $value
     * @return void
     */
    public function setValue($value);

    public function getValue();

    /**
     * @param array $options
     * @return void
     */
    public function setOptions(array $options);

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @return array
     */
    public function getFormField();

    /**
     * @return boolean
     */
    public function hideValue($value = null);

    /**
     * @return boolean
     */
    public function saveIfEmpty($value = null);

    /**
     * @return boolean
     */
    public function noValidate($value = null);

    /**
     * @param array $attributes
     * @return void
     */
    public function setFormElementAttributes(array $attributes);

    /**
     * @return array
     */
    public function getValidationRulesArray();
}
