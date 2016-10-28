<?php

namespace Anavel\Crud\Repository\Criteria;

use ANavallaSuiza\Laravel\Database\Contracts\Repository\Criteria;
use ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository;

class InArrayCriteria implements Criteria
{
    /**
     * @var string
     */
    protected $fieldName;
    /**
     * @var array
     */
    protected $fieldValues;

    public function __construct($fieldName, array $fieldValues)
    {
        $this->fieldName = $fieldName;
        $this->fieldValues = $fieldValues;
    }

    public function apply($model, Repository $repository)
    {
        return $model->whereIn($this->fieldName, $this->fieldValues);
    }
}
