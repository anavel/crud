<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory as ModelAbstractorFactoryContract;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory as RelationAbstractorFactoryContract;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory as FieldAbstractorFactoryContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use EasySlugger\Slugger;
use ANavallaSuiza\Crudoado\Contracts\Form\Generator as FormGenerator;
use ANavallaSuiza\Crudoado\Abstractor\Exceptions\FactoryException;

class ModelFactory implements ModelAbstractorFactoryContract
{
    protected $modelManager;
    protected $relationFactory;
    protected $fieldFactory;
    protected $slugger;

    protected $allConfiguredModels;
    /**
     * @var FormGenerator
     */
    private $generator;

    public function __construct(
        array $allConfiguredModels,
        ModelManager $modelManager,
        RelationAbstractorFactoryContract $relationFactory,
        FieldAbstractorFactoryContract $fieldFactory,
        FormGenerator $generator
    ) {
        $this->allConfiguredModels = $allConfiguredModels;
        $this->modelManager = $modelManager;
        $this->relationFactory = $relationFactory;
        $this->fieldFactory = $fieldFactory;
        $this->slugger = new Slugger();
        $this->generator = $generator;
    }

    /**
     * @param $slug
     * @param null $id
     * @return Model|null
     * @throws \Exception
     */
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

                $model = new Model($config, $this->modelManager->getAbstractionLayer($modelNamespace), $this->relationFactory, $this->generator);

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
            throw new FactoryException("Model ".$slug." not found on configuration");
        }

        return $model;
    }

    public function getByName($name, $id = null)
    {
        return $this->getBySlug($this->slugger->slugify($name));
    }

    public function getByClassName($classname, $id = null)
    {
        $model = new Model(['model' => $classname], $this->modelManager->getAbstractionLayer($classname), $this->relationFactory, $this->generator);

        return $model;
    }
}
