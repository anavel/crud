<?php
namespace Anavel\Crud\Repository\Criteria;

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
        $firstColumn = array_shift($this->columns);

        if (strpos($firstColumn, '.')) {
            $query = $this->setRelationFieldCondition($model, $firstColumn, false);
        } else {
            $query = $model->where($firstColumn, 'LIKE', '%'.$this->queryString.'%');
        }

        foreach ($this->columns as $column) {
            if (strpos($column, '.')) {
                $query = $this->setRelationFieldCondition($query, $column);
            } else {
                $query->orWhere($column, 'LIKE', '%'.$this->queryString.'%');
            }
        }

        return $query;
    }

    private function setRelationFieldCondition($query, $column, $or = true)
    {
        $columnRelation = explode('.', $column);

        if ($or) {
            $query->orWhereHas($columnRelation[0], function ($subquery) use ($columnRelation) {
                $subquery->where($columnRelation[1], 'LIKE', '%'.$this->queryString.'%');
            });
        } else {
            $query->whereHas($columnRelation[0], function ($subquery) use ($columnRelation) {
                $subquery->where($columnRelation[1], 'LIKE', '%'.$this->queryString.'%');
            });
        }

        return $query;
    }
}
