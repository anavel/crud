<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory as RelationAbstractorFactoryContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;

class RelationFactory implements RelationAbstractorFactoryContract
{
    protected $modelManager;

    protected $model;
    protected $config;

    public function __construct(ModelManager $modelManager)
    {
        $this->modelManager = $modelManager;
        $this->config = array();
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
    }

    public function get($name)
    {
        $relation = null;

        if (! method_exists($this->model, $name)) {
            throw new \Exception("Relation ".$name." does not exist on ".$this->model);
        }

        return $relation;
    }
}
