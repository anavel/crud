<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationConfig;
use Anavel\Crud\Repository\Criteria\InArrayCriteria;
use App;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Http\Request;

class SelectMultipleManyToMany extends SelectMultiple
{
    use CheckRelationCompatibility;

    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\BelongsToMany'
    );

    public function setup()
    {
        $this->checkRelationCompatibility();
        $this->checkDisplayConfig();
    }

    /**
     * @param array|null $relationArray
     * @return mixed
     */
    public function persist(array $relationArray = null, Request $request)
    {
        if (! empty($relationArray)) {
            $this->eloquentRelation->sync($relationArray[$this->eloquentRelation->getRelated()->getKeyName()]);
        }
    }

    /**
     * @param \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal
     * @return Column
     */
    protected function getColumn($dbal)
    {
        return $dbal->getTableColumn($this->eloquentRelation->getRelated()->getKeyName());
    }

}
