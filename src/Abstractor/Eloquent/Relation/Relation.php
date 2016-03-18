<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\ConfigurationReader;
use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationConfig;
use Anavel\Crud\Abstractor\Eloquent\Traits\ModelFields;
use Anavel\Crud\Contracts\Abstractor\Model as ModelAbstractor;
use Anavel\Crud\Contracts\Abstractor\Relation as RelationAbstractorContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Anavel\Crud\Contracts\Abstractor\FieldFactory as FieldFactoryContract;
use Illuminate\Support\Collection;

abstract class Relation implements RelationAbstractorContract
{
    use CheckRelationConfig;
    use ConfigurationReader;
    use ModelFields;

    const DISPLAY_TYPE_TAB = 'tab';
    const DISPLAY_TYPE_INLINE = 'inline';

    protected $name;
    protected $slug;
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
     * @var FieldFactoryContract
     */
    protected $fieldFactory;
    protected $modelManager;
    /** @var  ModelAbstractor */
    protected $modelAbstractor;
    /**
     * @var array
     */
    protected $config;

    public function __construct(array $config, ModelManager $modelManager, Model $model, EloquentRelation $eloquentRelation, FieldFactoryContract $fieldFactory)
    {
        $this->checkNameConfig($config);
        $this->name = $config['name'];
        $this->slug = $config['name'];
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
        foreach ($this->modelAbstractor->getRelations() as $relationKey => $relation) {
            /** @var RelationAbstractorContract $relation */
            foreach ($relation->getEditFields($relationKey) as $editGroupName => $editGroup) {
                $fields[$this->name][$editGroupName] = $editGroup;
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

    /**
     * @return Collection
     */
    public function getSecondaryRelations()
    {
        return $this->modelAbstractor->getRelations();
    }

    /**
     * @param Model $relatedModel
     * @return Relation
     */
    public function setRelatedModel(Model $relatedModel)
    {
        $this->relatedModel = $relatedModel;

        return $this;
    }
}
