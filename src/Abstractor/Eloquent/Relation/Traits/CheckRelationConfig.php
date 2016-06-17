<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation\Traits;

use Anavel\Crud\Abstractor\Exceptions\RelationException;

trait CheckRelationConfig
{
    public function checkDisplayConfig()
    {
        if (empty($this->config) || empty($this->config['display'])) {
            throw new RelationException('Display should be set in config');
        }
    }

    public function checkNameConfig($config)
    {
        if (empty($config) || empty($config['name'])) {
            throw new RelationException('Relation name should be set');
        }
    }
}
