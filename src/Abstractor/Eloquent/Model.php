<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractorContract;
use ANavallaSuiza\Crudoado\Abstractor\ConfigurationReader;
use ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer;
use Illuminate\Database\Eloquent\Model as LaravelModel;
use App;

class Model implements ModelAbstractorContract
{
    use ConfigurationReader;

    protected $dbal;

    protected $model;
    protected $config;

    protected $slug;
    protected $name;

    public function __construct($config, AbstractionLayer $dbal)
    {
        if (is_array($config)) {
            $this->model = $config['model'];
            $this->config = $config;
        } else {
            $this->model = $config;
            $this->config = [];
        }

        $this->dbal = $dbal;
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

                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function getEditRelations()
    {
        $configRelations =  $this->getConfigValue('relations');

        $relations = [];

        if (! empty($configRelations)) {
            foreach ($configRelations as $configRelation) {
                if (empty($configRelation['type'])) {
                    continue;
                }
                $className = 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\\' . ucfirst($configRelation['type']);

                $relations [] = new $className(App::make('ANavallaSuiza\Laravel\Database\Manager\Eloquent'), App::make($this->getModel()), $configRelation['name'], $configRelation['presentation']);
            }
        }

        return $relations;
    }

    protected function getReadOnlyColumns()
    {
        $columns = [LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT];

        $columns[] = $this->dbal->getModel()->getKeyName();

        return $columns;
    }
}
