<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits\CheckRelationConfig;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractor;
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
    /** @var  ModelAbstractor */
    protected $modelAbstractor;
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

        $this->modelAbstractor = \App::make('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory')->getByClassName(get_class($this->eloquentRelation->getRelated()), $this->config);
    }

    public function addSecondaryRelationFields(array $fields)
    {
        foreach ($this->modelAbstractor->getRelations() as $relation) {
            foreach ($relation->getEditFields() as $editField) {
                $fields[] = $editField;
            };
        }
        return $fields;
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
