<?php
namespace ANavallaSuiza\Crudoado\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\ModelAbstractor as ModelAbstractorContract;

class ModelAbstractor implements ModelAbstractorContract
{
    protected $dbal;

    protected $slug;
    protected $name;

    public function __construct()
    {

    }

    public function loadBySlug($slug)
    {

    }

    public function loadByName($name)
    {

    }

    public function getSlug()
    {

    }

    public function getName()
    {

    }

    public function getModel()
    {

    }

    public function isSoftDeletes()
    {

    }

    public function getListFields()
    {

    }

    public function getDetailFields()
    {

    }
}
