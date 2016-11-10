<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Http\Request;

class SelectMultipleManyToMany extends SelectMultiple
{
    use CheckRelationCompatibility;

    protected $compatibleEloquentRelations = [
        'Illuminate\Database\Eloquent\Relations\BelongsToMany',
    ];

    public function setup()
    {
        $this->checkRelationCompatibility();
        $this->checkDisplayConfig();
    }

    /**
     * @param array|null $relationArray
     *
     * @return mixed
     */
    public function persist(array $relationArray = null, Request $request)
    {
        if (!empty($relationArray)) {
            $array = $relationArray[$this->eloquentRelation->getRelated()->getKeyName()];
            if (in_array('', $array)) {
                $array = [];
            }
            $this->eloquentRelation->sync($array);
        }
    }

    /**
     * @param \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal
     *
     * @return Column
     */
    protected function getColumn($dbal)
    {
        return $dbal->getTableColumn($this->eloquentRelation->getRelated()->getKeyName());
    }
}
