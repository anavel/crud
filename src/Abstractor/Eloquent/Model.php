<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractorContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use EasySlugger\Slugger;

class Model implements ModelAbstractorContract
{
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

    }

    public function getListFields()
    {
        $tableColumns = $this->dbal->getTableColumns();

        $fields = array();
        foreach ($tableColumns as $name => $column) {
            $fields[] = new Field($name, $column->getType());
        }

        return $fields;
    }

    public function getDetailFields()
    {
        return [];
    }
}
