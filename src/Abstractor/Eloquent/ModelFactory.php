<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory as ModelAbstractorFactoryContract;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory as RelationAbstractorFactoryContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use EasySlugger\Slugger;

class ModelFactory implements ModelAbstractorFactoryContract
{
    protected $modelManager;
    protected $relationFactory;
    protected $slugger;

    protected $allConfiguredModels;

    public function __construct(array $allConfiguredModels, ModelManager $modelManager, RelationAbstractorFactoryContract $relationFactory)
    {
        $this->allConfiguredModels = $allConfiguredModels;
        $this->modelManager = $modelManager;
        $this->relationFactory = $relationFactory;
        $this->slugger = new Slugger();
    }

    public function getBySlug($slug, $id = null)
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

                $model = new Model($config, $this->modelManager->getAbstractionLayer($modelNamespace), $this->relationFactory);

                $model->setSlug($modelSlug)
                    ->setName($modelName);

                if (is_null($id)) {
                    $model->setInstance($this->modelManager->getModelInstance($modelNamespace));
                } else {
                    $repository = $this->modelManager->getRepository($modelNamespace);
                    $model->setInstance($repository->findByOrFail($repository->getModel()->getKeyName(), $id));
                }

                break;
            }
        }

        if (is_null($model)) {
            throw new \Exception("Model ".$slug." not found on configuration");
        }

        return $model;
    }

    public function getByName($name, $id = null)
    {
        return $this->getBySlug($this->slugger->slugify($name));
    }
}
