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
        return $model->where(function ($query) {
            $firstColumn = array_shift($this->columns);

            if (strpos($firstColumn, '.')) {
                $query = $this->setRelationFieldCondition($query, $firstColumn, false);
            } else {
                $query = $query->where($firstColumn, 'LIKE', '%'.$this->queryString.'%');
            }

            foreach ($this->columns as $column) {
                if (strpos($column, '.')) {
                    $query = $this->setRelationFieldCondition($query, $column);
                } else {
                    $query->orWhere($column, 'LIKE', '%'.$this->queryString.'%');
                }
            }
        });
    }

    private function setRelationFieldCondition($query, $column, $or = true)
    {
        $columnRelation = explode('.', $column);
        $firstRelation = array_shift($columnRelation);

        if ($or) {
            $query->orWhereHas($firstRelation, $this->getRelationClosure($columnRelation));
        } else {
            $query->whereHas($firstRelation, $this->getRelationClosure($columnRelation));
        }

        return $query;
    }

    private function getRelationClosure(array $relations)
    {
        return function ($query) use ($relations) {
            $relation = array_shift($relations);

            if (count($relations) > 0) {
                $query->whereHas($relation, $this->getRelationClosure($relations));
            } else {
                $query->where($relation, 'LIKE', '%'.$this->queryString.'%');
            }
        };
    }
}
