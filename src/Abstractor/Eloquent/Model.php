<?php

namespace Anavel\Crud\Abstractor\Eloquent;

use ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer;
use Anavel\Crud\Abstractor\ConfigurationReader;
use Anavel\Crud\Abstractor\Eloquent\Traits\HandleFiles;
use Anavel\Crud\Abstractor\Eloquent\Traits\ModelFields;
use Anavel\Crud\Abstractor\Exceptions\AbstractorException;
use Anavel\Crud\Contracts\Abstractor\Field as FieldContract;
use Anavel\Crud\Contracts\Abstractor\FieldFactory as FieldFactoryContract;
use Anavel\Crud\Contracts\Abstractor\Model as ModelAbstractorContract;
use Anavel\Crud\Contracts\Abstractor\Relation;
use Anavel\Crud\Contracts\Abstractor\RelationFactory as RelationFactoryContract;
use Anavel\Crud\Contracts\Form\Generator as FormGenerator;
use App;
use FormManager\ElementInterface;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Model implements ModelAbstractorContract
{
    use ConfigurationReader;
    use ModelFields;
    use HandleFiles;

    protected $dbal;
    protected $relationFactory;
    protected $fieldFactory;
    protected $generator;

    protected $model;
    protected $config;

    protected $slug;
    protected $name;
    protected $instance;

    /**
     * @var bool
     */
    protected $mustDeleteFilesInFilesystem;

    public function __construct(
        $config,
        AbstractionLayer $dbal,
        RelationFactoryContract $relationFactory,
        FieldFactoryContract $fieldFactory,
        FormGenerator $generator,
        $mustDeleteFilesInFilesystem = false
    ) {
        if (is_array($config)) {
            $this->model = $config['model'];
            $this->config = $config;
        } else {
            $this->model = $config;
            $this->config = [];
        }

        $this->dbal = $dbal;
        $this->relationFactory = $relationFactory;
        $this->fieldFactory = $fieldFactory;
        $this->generator = $generator;
        $this->mustDeleteFilesInFilesystem = $mustDeleteFilesInFilesystem;
    }

    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function setInstance($instance)
    {
        $this->instance = $instance;

        return $this;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    public function getName()
    {
        return transcrud($this->name);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function isSoftDeletes()
    {
        return $this->getConfigValue('soft_deletes') ? true : false;
    }

    public function getColumns($action, $withForeignKeys = false)
    {
        $tableColumns = $this->dbal->getTableColumns();

        $filteredColumns = [];
        foreach ($tableColumns as $name => $column) {
            $filteredColumns[str_replace('`', '', $name)] = $column;
        }
        $tableColumns = $filteredColumns;

        $foreignKeysName = [];
        if ($withForeignKeys === false) {
            $foreignKeys = $this->dbal->getTableForeignKeys();

            foreach ($foreignKeys as $foreignKey) {
                foreach ($foreignKey->getColumns() as $columnName) {
                    $foreignKeysName[] = $columnName;
                }
            }
        }

        $customDisplayedColumns = $this->getConfigValue($action, 'display');
        $customHiddenColumns = $this->getConfigValue($action, 'hide') ?: [];

        $columns = [];
        if (!empty($customDisplayedColumns) && is_array($customDisplayedColumns)) {
            foreach ($customDisplayedColumns as $customColumn) {
                if (strpos($customColumn, '.')) {
                    $customColumnRelation = explode('.', $customColumn);

                    $customColumnRelationFieldName = array_pop($customColumnRelation);

                    $modelAbstractor = $this;
                    foreach ($customColumnRelation as $relationName) {
                        $nestedRelation = $this->getNestedRelation($modelAbstractor, $relationName);
                        $modelAbstractor = $nestedRelation->getModelAbstractor();
                    }

                    $relationColumns = $nestedRelation->getModelAbstractor()->getColumns($action);

                    if (!array_key_exists($customColumnRelationFieldName, $relationColumns)) {
                        throw new AbstractorException('Column '.$customColumnRelationFieldName.' does not exist on relation '.implode('.', $customColumnRelation).' of model '.$this->getModel());
                    }

                    $columns[$customColumn] = $relationColumns[$customColumnRelationFieldName];
                } else {
                    if (!array_key_exists($customColumn, $tableColumns)) {
                        throw new AbstractorException('Column '.$customColumn.' does not exist on '.$this->getModel());
                    }

                    $columns[$customColumn] = $tableColumns[$customColumn];
                }
            }
        } else {
            foreach ($tableColumns as $name => $column) {
                if (in_array($name, $customHiddenColumns)) {
                    continue;
                }

                if (in_array($name, $foreignKeysName)) {
                    continue;
                }

                $columns[$name] = $column;
            }
        }

        return $columns;
    }

    protected function getNestedRelation(Model $modelAbstractor, $relationName)
    {
        $relations = $modelAbstractor->getRelations();

        if (!$relations->has($relationName)) {
            throw new AbstractorException('Relation '.$relationName.' not configured on '.$modelAbstractor->getModel());
        }

        $relation = $relations->get($relationName);

        if ($relation instanceof Relation) {
            return $relation;
        } else {
            return $relation['relation'];
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRelations()
    {
        $configRelations = $this->getConfigValue('relations');

        $relations = collect();

        if (!empty($configRelations)) {
            foreach ($configRelations as $relationName => $configRelation) {
                if (is_int($relationName)) {
                    $relationName = $configRelation;
                }

                $config = [];
                if ($configRelation !== $relationName) {
                    if (!is_array($configRelation)) {
                        $config['type'] = $configRelation;
                    } else {
                        $config = $configRelation;
                    }
                }

                /** @var Relation $relation */
                $relation = $this->relationFactory->setModel($this->instance)
                    ->setConfig($config)
                    ->get($relationName);

                $secondaryRelations = $relation->getSecondaryRelations();

                if (!$secondaryRelations->isEmpty()) {
                    $relations->put(
                        $relationName,
                        collect(['relation' => $relation, 'secondaryRelations' => $secondaryRelations])
                    );
                } else {
                    $relations->put($relationName, $relation);
                }
            }
        }

        return $relations;
    }

    /**
     * @param string|null $arrayKey
     *
     * @throws AbstractorException
     *
     * @return array
     */
    public function getListFields($arrayKey = 'main')
    {
        $columns = $this->getColumns('list');

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ?: [];

        $fields = [];
        foreach ($columns as $name => $column) {
            $presentation = null;
            if (array_key_exists($name, $fieldsPresentation)) {
                $presentation = $fieldsPresentation[$name];
            }

            $config = [
                'name'         => $name,
                'presentation' => $presentation,
                'form_type'    => null,
                'validation'   => null,
                'functions'    => null,
            ];

            $fields[$arrayKey][] = $this->fieldFactory
                ->setColumn($column)
                ->setConfig($config)
                ->get();
        }

        return $fields;
    }

    /**
     * @param string|null $arrayKey
     *
     * @throws AbstractorException
     *
     * @return array
     */
    public function getDetailFields($arrayKey = 'main')
    {
        $columns = $this->getColumns('detail');

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ?: [];

        $fields = [];
        foreach ($columns as $name => $column) {
            $presentation = null;
            if (array_key_exists($name, $fieldsPresentation)) {
                $presentation = $fieldsPresentation[$name];
            }

            $config = [
                'name'         => $name,
                'presentation' => $presentation,
                'form_type'    => null,
                'validation'   => null,
                'functions'    => null,
            ];

            $fields[$arrayKey][] = $this->fieldFactory
                ->setColumn($column)
                ->setConfig($config)
                ->get();
        }

        return $fields;
    }

    /**
     * @param bool|null   $withForeignKeys
     * @param string|null $arrayKey
     *
     * @throws AbstractorException
     *
     * @return array
     */
    public function getEditFields($withForeignKeys = false, $arrayKey = 'main')
    {
        $columns = $this->getColumns('edit', $withForeignKeys);

        $this->readConfig('edit');

        $fields = [];
        foreach ($columns as $name => $column) {
            if (!in_array($name, $this->getReadOnlyColumns())) {
                $presentation = null;
                if (array_key_exists($name, $this->fieldsPresentation)) {
                    $presentation = $this->fieldsPresentation[$name];
                }

                $config = [
                    'name'         => $name,
                    'presentation' => $presentation,
                    'form_type'    => null,
                    'validation'   => null,
                    'functions'    => null,
                ];

                $config = $this->setConfig($config, $name);

                $field = $this->fieldFactory
                    ->setColumn($column)
                    ->setConfig($config)
                    ->get();

                if (!empty($this->instance) && !empty($this->instance->getAttribute($name))) {
                    $field->setValue($this->instance->getAttribute($name));
                }

                $fields[$arrayKey][$name] = $field;

                if (!empty($config['form_type']) && $config['form_type'] === 'file') {
                    $field = $this->fieldFactory
                        ->setColumn($column)
                        ->setConfig([
                            'name'         => $name.'__delete',
                            'presentation' => null,
                            'form_type'    => 'checkbox',
                            'no_validate'  => true,
                            'functions'    => null,
                        ])
                        ->get();
                    $fields[$arrayKey][$name.'__delete'] = $field;
                }
            }
        }

        return $fields;
    }

    protected function getReadOnlyColumns()
    {
        $columns = [LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT, 'deleted_at'];

        $columns[] = $this->dbal->getModel()->getKeyName();

        return $columns;
    }

    /**
     * @param string $action
     *
     * @return ElementInterface
     */
    public function getForm($action)
    {
        $this->generator->setModelFields($this->getEditFields());
        $this->generator->setRelatedModelFields($this->getRelations());

        return $this->generator->getForm($action);
    }

    /**
     * @param array $requestForm
     *
     * @return mixed
     */
    public function persist(Request $request)
    {
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager $modelManager */
        $modelManager = App::make('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
        if (!empty($this->instance)) {
            $item = $this->instance;
        } else {
            $item = $modelManager->getModelInstance($this->getModel());
        }

        $fields = $this->getEditFields(true);
        $foreignFields = array_diff_key($fields['main'] ?? [], $this->getEditFields(false)['main'] ?? []);
        if (empty($fields['main']) && $this->getRelations()->isEmpty()) {
            return;
        }

        if (!empty($fields['main'])) {
            $skip = null;
            foreach ($fields['main'] as $key => $field) {
                /* @var FieldContract $field */
                if ($skip === $key) {
                    $skip = null;
                    continue;
                }
                $fieldName = $field->getName();
                $requestValue = $request->input("main.{$fieldName}");

                if (! empty($foreignFields) && (! empty($foreignFields[$fieldName])) && (empty($requestValue))) {
                    $requestValue = null;
                }

                if (get_class($field->getFormField()) === \FormManager\Fields\Checkbox::class) {
                    if (empty($requestValue)) {
                        // Unchecked checkboxes are not sent, so we force setting them to false
                        $item->setAttribute(
                            $fieldName,
                            $field->applyFunctions(null)
                        );
                    } else {
                        $requestValue = true;
                    }
                }

                if (get_class($field->getFormField()) === \FormManager\Fields\File::class) {
                    $handleResult = $this->handleField($request, $item, $fields['main'], 'main', $fieldName, $this->mustDeleteFilesInFilesystem);
                    if (!empty($handleResult['skip'])) {
                        $skip = $handleResult['skip'];
                    }
                    if (!empty($handleResult['requestValue'])) {
                        $requestValue = $handleResult['requestValue'];
                    }
                }

                if (!$field->saveIfEmpty() && empty($requestValue)) {
                    continue;
                }

                if (! empty($requestValue)
                    || (empty($requestValue) && !empty($item->getAttribute($fieldName)))
                    || (! empty($foreignFields) && (! empty($foreignFields[$fieldName])) && (empty($requestValue)))
                ) {
                    $item->setAttribute(
                        $fieldName,
                        $field->applyFunctions($requestValue)
                    );
                }
            }
        }

        $item->save();

        $this->setInstance($item);

        if (!empty($relations = $this->getRelations())) {
            foreach ($relations as $relationKey => $relation) {
                if ($relation instanceof Collection) {
                    $input = $request->input($relationKey);
                    /** @var $relationInstance Relation */
                    $relationInstance = $relation->get('relation');
                    $relationInstance->persist($input, $request);
                } else {
                    /* @var $relation Relation */
                    $relation->persist($request->input($relationKey), $request);
                }
            }
        }

        return $item;
    }

    /**
     * @return array
     */
    public function getValidationRules()
    {
        return $this->generator->getValidationRules();
    }

    public function getFieldValue($item, $fieldName)
    {
        $value = null;

        if (strpos($fieldName, '.')) {
            $customColumnRelation = explode('.', $fieldName);

            $customColumnRelationFieldName = array_pop($customColumnRelation);

            $entity = $item;
            $relation = null;
            $relationName = '';
            foreach ($customColumnRelation as $relationName) {
                $relation = $entity->{$relationName};
                if ($relation instanceof Collection) {
                    $entity = $relation->first();
                } else {
                    $entity = $relation;
                }
            }

            if (is_null($entity)) {
                return;
            }
            $lastRelationName = $relationName;

            array_pop($customColumnRelation);

            $prevModelAbtractor = $this;
            foreach ($customColumnRelation as $relationName) {
                $nestedRelation = $this->getNestedRelation($prevModelAbtractor, $relationName);
                $prevModelAbtractor = $nestedRelation->getModelAbstractor();
            }

            if (empty($relation)) {
                return;
            }

            if ($relation instanceof Collection) {
                $relations = $prevModelAbtractor->getRelations();

                $relationAbstractor = $relations->get($lastRelationName);

                if ($relationAbstractor instanceof \Anavel\Crud\Abstractor\Eloquent\Relation\Translation) {
                    $value = $entity->getAttribute($customColumnRelationFieldName);
                } else {
                    $value = $relation->implode($customColumnRelationFieldName, ', ');
                }
            } else {
                $value = $relation->getAttribute($customColumnRelationFieldName);
            }
        } else {
            $value = $item->getAttribute($fieldName);
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function mustDeleteFilesInFilesystem()
    {
        return $this->mustDeleteFilesInFilesystem;
    }
}
