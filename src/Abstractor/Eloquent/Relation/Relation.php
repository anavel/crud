<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits\CheckRelationConfig;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation as RelationAbstractorContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory;

abstract class Relation implements RelationAbstractorContract
{
    use CheckRelationConfig;

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
    /**
     * @var FieldFactory
     */
    protected $fieldFactory;
    protected $modelManager;
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config, ModelManager $modelManager, Model $model, EloquentRelation $eloquentRelation, FieldFactory $fieldFactory)
    {
        $this->checkNameConfig($config);
        $this->name = $config['name'];
        $this->relatedModel = $model;
        $this->eloquentRelation = $eloquentRelation;
        $this->fieldFactory = $fieldFactory;

        $this->modelManager = $modelManager;

        $this->config = $config;
        $this->setup();
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
