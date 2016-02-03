<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationConfig;
use Anavel\Crud\Contracts\Abstractor\Model as ModelAbstractor;
use Anavel\Crud\Contracts\Abstractor\Relation as RelationAbstractorContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Anavel\Crud\Contracts\Abstractor\FieldFactory;

abstract class Relation implements RelationAbstractorContract
{
    use CheckRelationConfig;

    const DISPLAY_TYPE_TAB = 'tab';
    const DISPLAY_TYPE_INLINE = 'inline';

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

        $this->modelAbstractor = \App::make('Anavel\Crud\Contracts\Abstractor\ModelFactory')->getByClassName(get_class($this->eloquentRelation->getRelated()), $this->config);
    }

    public function addSecondaryRelationFields(array $fields)
    {
        foreach ($this->modelAbstractor->getRelations() as $relation) {
            foreach ($relation->getEditFields() as $editField) {
                $fields[$this->name][] = $editField;
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
