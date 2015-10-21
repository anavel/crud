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
        return $this->getConfigValue('soft_deletes');
    }

    public function getListFields()
    {
        $tableColumns = $this->dbal->getTableColumns();

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];

        $fields = array();
        foreach ($tableColumns as $name => $column) {
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
        $tableColumns = $this->dbal->getTableColumns();

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];

        $fields = array();
        foreach ($tableColumns as $name => $column) {
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
        $tableColumns = $this->dbal->getTableColumns();

        $fieldsPresentation = $this->getConfigValue('fields_presentation') ? : [];

        $fields = array();
        foreach ($tableColumns as $name => $column) {
            if (! in_array($name, $this->getReadOnlyFields())) {
                $presentation = null;
                if (array_key_exists($name, $fieldsPresentation)) {
                    $presentation = $fieldsPresentation[$name];
                }

                $fields[] = new Field($column, $name, $presentation);
            }
        }

        return $fields;
    }

    protected function getReadOnlyFields()
    {
        $fields = [LaravelModel::CREATED_AT, LaravelModel::UPDATED_AT];

        $fields[] = $this->dbal->getModel()->getKeyName();

        return $fields;
    }
}
