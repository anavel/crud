<?php
namespace ANavallaSuiza\Crudoado\Repository\Criteria;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\Criteria;
use ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository;

class SearchCriteria implements Criteria
{
    protected $columns;
    protected $queryString;

    public function __construct(array $columns, $queryString)
    {
        $this->columns = $columns;
        $this->queryString = $queryString;
    }

    public function apply($model, Repository $repository)
    {
        $query = $model->where(array_shift($this->columns), 'LIKE', '%'.$this->queryString.'%');

        foreach ($this->columns as $column) {
            $query->orWhere($column, 'LIKE', '%'.$this->queryString.'%');
        }

        return $query;
    }
}
