<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractorContract;
use ANavallaSuiza\Crudoado\Abstractor\ConfigurationReader;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use EasySlugger\Slugger;
use Illuminate\Database\Eloquent\Model as LaravelModel;

class Model implements ModelAbstractorContract
{
    use ConfigurationReader;

    protected $modelManager;
    protected $slugger;
    protected $dbal;

    protected $allConfiguredModels;
    protected $model;
    protected $config;

    protected $slug;
    protected $name;

    public function __construct(array $allConfiguredModels, ModelManager $modelManager)
    {
        $this->allConfiguredModels = $allConfiguredModels;
        $this->modelManager = $modelManager;
        $this->slugger = new Slugger();
    }

    public function loadBySlug($slug)
    {
        foreach ($this->allConfiguredModels as $modelName => $config) {
            $modelSlug = $this->slugger->slugify($modelName);

            if ($modelSlug === $slug) {
                $this->slug = $modelSlug;
                $this->name = $modelName;

                if (is_array($config)) {
                    $this->model = $config['model'];
                    $this->config = $config;
                } else {
                    $this->model = $config;
                    $this->config = [];
                }

                $this->dbal = $this->modelManager->getAbstractionLayer($this->model);

                break;
            }
        }
    }

    public function loadByName($name)
    {
        $this->loadBySlug($this->slugger->slugify($name));
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

    }

    protected function getReadOnlyColumns()
    {
        $columns = [LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT];

        $columns[] = $this->dbal->getModel()->getKeyName();

        return $columns;
    }
}
