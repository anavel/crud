<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractorContract;
use ANavallaSuiza\Crudoado\Abstractor\ConfigurationReader;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory;
use ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer;
use FormManager\ElementInterface;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use App;
use ANavallaSuiza\Crudoado\Contracts\Form\Generator as FormGenerator;
use Illuminate\Http\Request;

class Model implements ModelAbstractorContract
{
    use ConfigurationReader;

    protected $dbal;
    protected $relationFactory;
    protected $generator;

    protected $model;
    protected $config;

    protected $slug;
    protected $name;
    protected $instance;

    public function __construct($config, AbstractionLayer $dbal, RelationFactory $relationFactory, FormGenerator $generator)
    {
        if (is_array($config)) {
            $this->model = $config['model'];
            $this->config = $config;
        } else {
            $this->model = $config;
            $this->config = [];
        }

        $this->dbal = $dbal;
        $this->relationFactory = $relationFactory;
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
        return $this->name;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getInstance()
    {
        return $this->intance;
    }

    public function isSoftDeletes()
    {
        return $this->getConfigValue('soft_deletes') ? true : false;
    }

    protected function getColumns($action)
    {
        $tableColumns = $this->dbal->getTableColumns();

        $customDisplayedColumns = $this->getConfigValue($action, 'display');
        $customHiddenColumns = $this->getConfigValue($action, 'hide') ? : [];

        $columns = array();
        if (! empty($customDisplayedColumns) && is_array($customDisplayedColumns)) {
            foreach ($customDisplayedColumns as $customColumn) {
                if (! array_key_exists($customColumn, $tableColumns)) {
                    throw new \Exception("Column ".$customColumn." does not exist on ".$this->getModel());
                }

                $columns[$customColumn] = $tableColumns[$customColumn];
            }
        } else {
            foreach ($tableColumns as $name => $column) {
                if (in_array($name, $customHiddenColumns)) {
                    continue;
                }

                $columns[$name] = $column;
            }
        }

        return $columns;
    }

    public function getRelations()
    {
        $configRelations =  $this->getConfigValue('relations');

        $relations = [];

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

                $relations[] = $this->relationFactory->setModel($this->instance)
                    ->setConfig($config)
                    ->get($relationName);
            }
        }

        return $relations;
    }

    public function getListFields()
    {
        $columns = $this->getColumns('list');

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];

        $fields = array();
        foreach ($columns as $name => $column) {
            $presentation = null;
            if (array_key_exists($name, $fieldsPresentation)) {
                $presentation = $fieldsPresentation[$name];
            }

            $fields[] = new Field($column, $name, $presentation);
        }

        return $fields;
    }

    public function getDetailFields()
    {
        $columns = $this->getColumns('detail');

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];

        $fields = array();
        foreach ($columns as $name => $column) {
            $presentation = null;
            if (array_key_exists($name, $fieldsPresentation)) {
                $presentation = $fieldsPresentation[$name];
            }

            $fields[] = new Field($column, $name, $presentation);
        }

        return $fields;
    }

    public function getEditFields()
    {
        $columns = $this->getColumns('edit');

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];
        $formTypes = $this->getConfigValue('edit', 'form_types') ? : [];
        $validationRules = $this->getConfigValue('edit', 'validation') ? : [];
        $functions = $this->getConfigValue('edit', 'functions') ? : [];

        $fields = array();
        foreach ($columns as $name => $column) {
            if (! in_array($name, $this->getReadOnlyColumns())) {
                $presentation = null;
                if (array_key_exists($name, $fieldsPresentation)) {
                    $presentation = $fieldsPresentation[$name];
                }

                $field = new Field($column, $name, $presentation);

                if (array_key_exists($name, $formTypes)) {
                    $field->setCustomFormType($formTypes[$name]);
                }

                if (array_key_exists($name, $validationRules)) {
                    $field->setValidationRules($validationRules[$name]);
                }

                if (array_key_exists($name, $functions)) {
                    $field->setFunctions($functions[$name]);
                }

                if (! empty($this->instance) && ! empty($this->instance->getAttribute($name))) {
                    $field->setValue($this->instance->getAttribute($name));
                }

                $fields[] = $field;
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
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request)
    {

        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager $modelManager */
        $modelManager = \App::make('ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager');
        $item = $modelManager->getModelInstance($this->getModel());

        foreach ($this->getEditFields() as $field) {
            $item->setAttribute(
                $field->getName(),
                $field->applyFunctions($request->input($field->getName()))
            );
        }

        $item->save();

        $this->setInstance($item);

        if (! empty($relations = $this->getRelations())) {
            foreach ($relations as $relation) {
                $relation->persist($request);
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
