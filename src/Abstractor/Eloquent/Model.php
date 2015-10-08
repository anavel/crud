<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractorContract;
use EasySlugger\Slugger;

class Model implements ModelAbstractorContract
{
    protected $dbal;
    protected $slugger;

    protected $allConfiguredModels;
    protected $model;
    protected $config;

    protected $slug;
    protected $name;

    public function __construct(array $allConfiguredModels)
    {
        $this->allConfiguredModels = $allConfiguredModels;
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
        return [];
    }

    public function getDetailFields()
    {
        return [];
    }
}
