<?php

namespace Anavel\Crud\Abstractor\Eloquent;

use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Anavel\Crud\Abstractor\Exceptions\FactoryException;
use Anavel\Crud\Contracts\Abstractor\FieldFactory as FieldAbstractorFactoryContract;
use Anavel\Crud\Contracts\Abstractor\ModelFactory as ModelAbstractorFactoryContract;
use Anavel\Crud\Contracts\Abstractor\RelationFactory as RelationAbstractorFactoryContract;
use Anavel\Crud\Contracts\Form\Generator as FormGenerator;
use Anavel\Foundation\Contracts\Anavel;
use EasySlugger\Slugger;
use ReflectionClass;

class ModelFactory implements ModelAbstractorFactoryContract
{
    protected $modelManager;
    protected $relationFactory;
    protected $fieldFactory;
    protected $slugger;

    protected $allConfiguredModels;

    /**
     * @var Anavel
     */
    protected $anavel;
    /**
     * @var FormGenerator
     */
    private $generator;

    /**
     * ModelFactory constructor.
     *
     * @param array                             $allConfiguredModels
     * @param ModelManager                      $modelManager
     * @param RelationAbstractorFactoryContract $relationFactory
     * @param FieldAbstractorFactoryContract    $fieldFactory
     * @param FormGenerator                     $generator
     * @param Anavel                            $anavel
     */
    public function __construct(
        array $allConfiguredModels,
        ModelManager $modelManager,
        RelationAbstractorFactoryContract $relationFactory,
        FieldAbstractorFactoryContract $fieldFactory,
        FormGenerator $generator,
        Anavel $anavel
    ) {
        $this->allConfiguredModels = $allConfiguredModels;
        $this->modelManager = $modelManager;
        $this->relationFactory = $relationFactory;
        $this->fieldFactory = $fieldFactory;
        $this->slugger = new Slugger();
        $this->generator = $generator;
        $this->anavel = $anavel;
    }

    /**
     * @param $slug
     * @param null $id
     *
     * @throws \Exception
     *
     * @return Model|null
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

                $model = new Model($config, $this->modelManager->getAbstractionLayer($modelNamespace),
                    $this->relationFactory, $this->fieldFactory, $this->generator,
                    !$this->anavel->hasModule('Anavel\Uploads\UploadsModuleProvider'));

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
            throw new FactoryException('Model '.$slug.' not found on configuration');
        }

        return $model;
    }

    public function getByName($name, $id = null)
    {
        return $this->getBySlug($this->slugger->slugify($name));
    }

    public function getByClassName($classname, array $config, $id = null)
    {
        $model = new Model(array_merge(['model' => $classname], $config), $this->modelManager->getAbstractionLayer($classname), $this->relationFactory, $this->fieldFactory, $this->generator);
        $model->setSlug($this->slugger->slugify((new ReflectionClass($classname))->getShortName()));

        if (is_null($id)) {
            $model->setInstance($this->modelManager->getModelInstance($classname));
        } else {
            $repository = $this->modelManager->getRepository($classname);
            $model->setInstance($repository->findByOrFail($repository->getModel()->getKeyName(), $id));
        }

        return $model;
    }
}
