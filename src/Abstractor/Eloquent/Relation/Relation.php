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

    public function __construct(ModelManager $modelManager, Model $model, EloquentRelation $eloquentRelation, $name, $presentation = null)
    {
        $this->name = $name;
        $this->presentation = $presentation;
        $this->relatedModel = $model;
        $this->eloquentRelation = $eloquentRelation;

        $this->modelManager = $modelManager;
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
}
