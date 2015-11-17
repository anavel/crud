<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation as RelationAbstractorContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

abstract class Relation implements RelationAbstractorContract
{
    protected $name;
    protected $presentation;
    protected $type;
    /**
     * @var Model
     */
    protected $relatedModel;
    /**
     * @var EloquentRelation
     */
    protected $eloquentRelation;
    protected $modelManager;
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config, ModelManager $modelManager, Model $model, EloquentRelation $eloquentRelation)
    {
        $this->name = $config['name'];
        $this->relatedModel = $model;
        $this->eloquentRelation = $eloquentRelation;

        $this->modelManager = $modelManager;

        if ($this->checkEloquentRelationCompatibility() === false) {
            throw new \Exception(get_class($this->eloquentRelation)." eloquent relation is not compatible with ".$this->getType()." type");
        }
        $this->config = $config;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPresentation()
    {
        return $this->presentation ? : ucfirst(str_replace('_', ' ', $this->name));
    }

    public function getType()
    {
        return get_class($this);
    }

    /**
     * @return boolean
     */
    abstract public function checkEloquentRelationCompatibility();
}
