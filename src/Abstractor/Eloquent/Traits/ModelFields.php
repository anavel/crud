<?php

namespace Anavel\Crud\Abstractor\Eloquent\Traits;

trait ModelFields
{
    protected $fieldsPresentation = [];
    protected $formTypes = [];
    protected $validationRules = [];
    protected $functions = [];
    protected $defaults = [];

    /**
     * @param string $action
     */
    public function readConfig($action)
    {
        $this->fieldsPresentation = $this->getConfigValue('fields_presentation') ?: [];
        $this->formTypes = $this->getConfigValue($action, 'form_types') ?: [];
        $this->validationRules = $this->getConfigValue($action, 'validation') ?: [];
        $this->functions = $this->getConfigValue($action, 'functions') ?: [];
        $this->defaults = $this->getConfigValue($action, 'defaults') ?: [];
    }

    /**
     * @param array  $config
     * @param string $columnName
     *
     * @return array
     */
    public function setConfig(array $config, $columnName)
    {
        if (array_key_exists($columnName, $this->formTypes)) {
            $config['form_type'] = $this->formTypes[$columnName];
        }

        if (array_key_exists($columnName, $this->validationRules)) {
            $config['validation'] = $this->validationRules[$columnName];
        }

        if (array_key_exists($columnName, $this->functions)) {
            $config['functions'] = $this->functions[$columnName];
        }

        if (array_key_exists($columnName, $this->defaults)) {
            $config['defaults'] = $this->defaults[$columnName];
        }

        return $config;
    }
}
