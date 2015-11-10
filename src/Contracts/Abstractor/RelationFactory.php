<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface RelationFactory
{
    public function setModel($model);

    /**
     * Relation config
     *
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * Generates a relation instance
     *
     * @param string $name Relation name
     *
     * @return string
     */
    public function get($name);
}
