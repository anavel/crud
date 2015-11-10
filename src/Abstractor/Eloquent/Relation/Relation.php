<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation as RelationAbstractorContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Illuminate\Database\Eloquent\Model;

abstract class Relation implements RelationAbstractorContract
{
    protected $name;
    protected $presentation;
    protected $type;
    /**
     * @var Model
     */
    protected $relatedModel;
    protected $modelManager;

    public function __construct(ModelManager $modelManager, Model $model, $name, $presentation)
    {
        $this->name = $name;
        $this->presentation = $presentation;
        $this->relatedModel = $model;

        $this->modelManager = $modelManager;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPresentation()
    {
        return $this->presentation;
    }

    public function getType()
    {
        return get_class($this);
    }
}
