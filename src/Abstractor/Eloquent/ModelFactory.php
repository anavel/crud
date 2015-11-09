<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory as ModelAbstractorFactoryContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use EasySlugger\Slugger;

class ModelFactory implements ModelAbstractorFactoryContract
{
    protected $modelManager;
    protected $slugger;

    protected $allConfiguredModels;

    public function __construct(array $allConfiguredModels, ModelManager $modelManager)
    {
        $this->allConfiguredModels = $allConfiguredModels;
        $this->modelManager = $modelManager;
        $this->slugger = new Slugger();
    }

    public function getBySlug($slug)
    {
        $model = null;

        foreach ($this->allConfiguredModels as $modelName => $config) {
            $modelSlug = $this->slugger->slugify($modelName);

            if ($modelSlug === $slug) {
                if (is_array($config)) {
                    $modelNamespace = $config['model'];
                } else {
                    $modelNamespace = $config;
                }

                $model = new Model($config, $this->modelManager->getAbstractionLayer($modelNamespace));

                $model->setSlug($modelSlug)
                    ->setName($modelName);

                break;
            }
        }

        if (is_null($model)) {
            throw new \Exception("Model ".$slug." not found on configuration");
        }

        return $model;
    }

    public function getByName($name)
    {
        return $this->getBySlug($this->slugger->slugify($name));
    }
}
