<?php

namespace Anavel\Crud\Repository\Criteria;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\Criteria;
use ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository;

class WithCriteria implements Criteria
{
    protected $relation;

    public function __construct($relation)
    {
        $this->relation = explode('.', $relation);
    }

    public function apply($model, Repository $repository)
    {
        return $this->setWith($model);
    }

    private function setWith($model)
    {
        if (empty($this->relation)) {
            return $model;
        }

        return $model->with([array_shift($this->relation) => function ($q) {
            $this->setWith($q);
        }]);
    }
}
