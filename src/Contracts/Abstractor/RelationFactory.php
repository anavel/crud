<?php
namespace Anavel\Crud\Contracts\Abstractor;

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
     * Factory method to create relation instances.
     *
     * @param string $name The name of the relation
     *
     * @return \Anavel\Crud\Abstractor\Eloquent\Relation\Relation
     *
     * @throws \Exception
     */
    public function get($name);
}
