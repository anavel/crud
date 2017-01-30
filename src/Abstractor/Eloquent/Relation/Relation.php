<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Anavel\Crud\Abstractor\ConfigurationReader;
use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationConfig;
use Anavel\Crud\Abstractor\Eloquent\Traits\ModelFields;
use Anavel\Crud\Contracts\Abstractor\FieldFactory as FieldFactoryContract;
use Anavel\Crud\Contracts\Abstractor\Model as ModelAbstractor;
use Anavel\Crud\Contracts\Abstractor\Relation as RelationAbstractorContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
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
    /** @var ModelAbstractor */
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

        $relatedModelClassName = get_class($this->eloquentRelation->getRelated());
        $relatedmodelRelationsConfig = [];

        foreach (config('anavel-crud.models') as $modelConfig) {
            if (is_array($modelConfig) && array_key_exists('model', $modelConfig) && $relatedModelClassName == $modelConfig['model']) {
                if (array_key_exists('relations', $modelConfig)) {
                    $relatedmodelRelationsConfig['relations'] = $modelConfig['relations'];
                }
            }
        }

        $config = array_merge($this->config, $relatedmodelRelationsConfig);

        $this->modelAbstractor = \App::make('Anavel\Crud\Contracts\Abstractor\ModelFactory')->getByClassName(get_class($this->eloquentRelation->getRelated()), $config);
    }

    public function addSecondaryRelationFields(array $fields)
    {
        foreach ($this->modelAbstractor->getRelations() as $relationKey => $relation) {
            /** @var RelationAbstractorContract $relation */
            foreach ($relation->getEditFields($relationKey) as $editGroupName => $editGroup) {
                $fields[$this->name][$editGroupName] = $editGroup;
            }
        }

        return $fields;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * return null|string.
     */
    public function getDisplay()
    {
        if (!empty($this->config['display'])) {
            return $this->config['display'];
        }
    }

    public function getPresentation()
    {
        if ($this->presentation) {
            return transcrud($this->presentation);
        }

        $nameWithSpaces = str_replace('_', ' ', $this->name);
        $namePieces = explode(' ', $nameWithSpaces);
        $namePieces = array_filter(array_map('trim', $namePieces));

        return transcrud(ucfirst(implode(' ', $namePieces)));
    }

    public function getType()
    {
        return get_class($this);
    }

    public function getModelAbstractor()
    {
        return $this->modelAbstractor;
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
     *
     * @return Relation
     */
    public function setRelatedModel(Model $relatedModel)
    {
        $this->relatedModel = $relatedModel;

        return $this;
    }
}
