<?php

namespace Anavel\Crud\Abstractor;

trait ConfigurationReader
{
    public function getConfigValue()
    {
        if (!property_exists($this, 'config') || !is_array($this->config) || empty($this->config)) {
            return;
        }

        $params = func_get_args();

        $lastParam = array_pop($params);

        $nestedConfig = $this->config;

        if (is_array($params) && count($params) > 0) {
            foreach ($params as $configKey) {
                if (!array_key_exists($configKey, $nestedConfig)) {
                    $nestedConfig = [];

                    return;
                }

                $nestedConfig = $nestedConfig[$configKey];
            }
        }

        if (array_key_exists($lastParam, $nestedConfig)) {
            return $nestedConfig[$lastParam];
        }
    }
}
