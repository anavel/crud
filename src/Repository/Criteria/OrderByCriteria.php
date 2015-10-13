<?php
namespace ANavallaSuiza\Crudoado\Repository\Criteria;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\Criteria;
use ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository;

class OrderByCriteria implements Criteria
{
    protected $byColumn;
    protected $reverse;

    public function __construct($byColumn, $reverse = false)
    {
        $this->byColumn = $byColumn;
        $this->reverse = $reverse;
    }

    public function apply($model, Repository $repository)
    {
        return $model->orderBy($this->byColumn, ($this->reverse ? 'DESC' : 'ASC'));
    }
}
