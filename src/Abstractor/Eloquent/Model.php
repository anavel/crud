<?php
namespace Anavel\Crud\Abstractor\Eloquent;

use Anavel\Crud\Abstractor\Eloquent\Traits\HandleFiles;
use Anavel\Crud\Abstractor\Eloquent\Traits\ModelFields;
use Anavel\Crud\Contracts\Abstractor\Field as FieldContract;
use Anavel\Crud\Contracts\Abstractor\Model as ModelAbstractorContract;
use Anavel\Crud\Abstractor\ConfigurationReader;
use Anavel\Crud\Contracts\Abstractor\Relation;
use Anavel\Crud\Contracts\Abstractor\RelationFactory as RelationFactoryContract;
use Anavel\Crud\Contracts\Abstractor\FieldFactory as FieldFactoryContract;
use ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer;
use FormManager\ElementInterface;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use App;
use Anavel\Crud\Contracts\Form\Generator as FormGenerator;
use Anavel\Crud\Abstractor\Exceptions\AbstractorException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

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

    public function __construct(
        $config,
        AbstractionLayer $dbal,
        RelationFactoryContract $relationFactory,
        FieldFactoryContract $fieldFactory,
        FormGenerator $generator
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
        $customHiddenColumns = $this->getConfigValue($action, 'hide') ? : [];

        $columns = array();
        if (! empty($customDisplayedColumns) && is_array($customDisplayedColumns)) {
            foreach ($customDisplayedColumns as $customColumn) {
                if (! array_key_exists($customColumn, $tableColumns)) {
                    throw new AbstractorException("Column " . $customColumn . " does not exist on " . $this->getModel());
                }

                $columns[$customColumn] = $tableColumns[$customColumn];
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

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getRelations()
    {
        $configRelations = $this->getConfigValue('relations');

        $relations = collect();

        if (! empty($configRelations)) {
            foreach ($configRelations as $relationName => $configRelation) {
                if (is_int($relationName)) {
                    $relationName = $configRelation;
                }

                $config = [];
                if ($configRelation !== $relationName) {
                    if (! is_array($configRelation)) {
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


                if (! $secondaryRelations->isEmpty()) {
                    $relations->put($relationName,
                        collect(['relation' => $relation, 'secondaryRelations' => $secondaryRelations]));
                } else {
                    $relations->put($relationName, $relation);
                }

            }
        }

        return $relations;
    }

    /**
     * @param string|null $arrayKey
     * @return array
     * @throws AbstractorException
     */
    public function getListFields($arrayKey = 'main')
    {
        $columns = $this->getColumns('list');

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];

        $fields = array();
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
                'functions'    => null
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
     * @return array
     * @throws AbstractorException
     */
    public function getDetailFields($arrayKey = 'main')
    {
        $columns = $this->getColumns('detail');

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];

        $fields = array();
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
                'functions'    => null
            ];

            $fields[$arrayKey][] = $this->fieldFactory
                ->setColumn($column)
                ->setConfig($config)
                ->get();
        }

        return $fields;
    }

    /**
     * @param bool|null $withForeignKeys
     * @param string|null $arrayKey
     * @return array
     * @throws AbstractorException
     */
    public function getEditFields($withForeignKeys = false, $arrayKey = 'main')
    {
        $columns = $this->getColumns('edit', $withForeignKeys);

        $this->readConfig('edit');

        $fields = array();
        foreach ($columns as $name => $column) {
            if (! in_array($name, $this->getReadOnlyColumns())) {
                $presentation = null;
                if (array_key_exists($name, $this->fieldsPresentation)) {
                    $presentation = $this->fieldsPresentation[$name];
                }

                $config = [
                    'name'         => $name,
                    'presentation' => $presentation,
                    'form_type'    => null,
                    'validation'   => null,
                    'functions'    => null
                ];

                $config = $this->setConfig($config, $name);

                $field = $this->fieldFactory
                    ->setColumn($column)
                    ->setConfig($config)
                    ->get();

                if (! empty($this->instance) && ! empty($this->instance->getAttribute($name))) {
                    $field->setValue($this->instance->getAttribute($name));
                }

                $fields[$arrayKey][$name] = $field;

                if (! empty($config['form_type']) && $config['form_type'] === 'file') {
                    $field = $this->fieldFactory
                        ->setColumn($column)
                        ->setConfig([
                            'name'         => $name . '__delete',
                            'presentation' => null,
                            'form_type'    => 'checkbox',
                            'no_validate'  => true,
                            'functions'    => null
                        ])
                        ->get();
                    $fields[$arrayKey][$name . '__delete'] = $field;
                }
            }
        }

        return $fields;
    }

    protected function getReadOnlyColumns()
    {
        $columns = [LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT];

        $columns[] = $this->dbal->getModel()->getKeyName();

        return $columns;
    }

    /**
     * @param string $action
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
     * @return mixed
     */
    public function persist(Request $request)
    {
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager $modelManager */
        $modelManager = App::make('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
        if (! empty($this->instance)) {
            $item = $this->instance;
        } else {
            $item = $modelManager->getModelInstance($this->getModel());
        }


        $fields = $this->getEditFields(true);
        if (empty($fields['main']) && $this->getRelations()->isEmpty()) {
            return;
        }

        if (! empty($fields['main'])) {
            $skip = null;
            foreach ($fields['main'] as $key => $field) {
                /** @var FieldContract $field */
                if ($skip === $key) {
                    $skip = null;
                    continue;
                }
                $fieldName = $field->getName();
                $requestValue = $request->input("main.{$fieldName}");

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
                    $handleResult = $this->handleField($request, $item, $fields['main'], 'main', $fieldName);
                    if (! empty($handleResult['skip'])) {
                        $skip = $handleResult['skip'];
                    }
                    if (! empty($handleResult['requestValue'])) {
                        $requestValue = $handleResult['requestValue'];
                    }
                }


                if (! $field->saveIfEmpty() && empty($requestValue)) {
                    continue;
                }

                if (! empty($requestValue)) {
                    $item->setAttribute(
                        $fieldName,
                        $field->applyFunctions($requestValue)
                    );
                }
            }
        }

        $item->save();

        $this->setInstance($item);


        if (! empty($relations = $this->getRelations())) {
            foreach ($relations as $relationKey => $relation) {
                if ($relation instanceof Collection) {
                    $input = $request->input($relationKey);
                    $relation->get('relation')->persist($input, $request);
                } else {
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
}
