<?php


namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits;


use ANavallaSuiza\Crudoado\Abstractor\Exceptions\RelationException;

trait CheckRelationConfig
{
    public function checkDisplayConfig()
    {
        if (empty($this->config) || empty($this->config['display'])) {
            throw new RelationException('Display should be set in config');
        }
    }
}