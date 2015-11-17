<?php
namespace ANavallaSuiza\Crudoado\Repository\Criteria;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\Criteria;
use ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository;

class InArrayCriteria implements Criteria
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function apply($model, Repository $repository)
    {
        return $model->whereIn($this->ids);
    }
}
