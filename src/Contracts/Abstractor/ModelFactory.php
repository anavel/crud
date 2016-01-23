<?php
namespace Anavel\Crud\Contracts\Abstractor;

interface ModelFactory
{
    /**
     *
     * @return Model
     */
    public function getBySlug($slug, $id = null);

    /**
     *
     * @return Model
     */
    public function getByName($name, $id = null);

    /**
     *
     * @return Model
     */
    public function getByClassName($classname, array $config, $id = null);
}
