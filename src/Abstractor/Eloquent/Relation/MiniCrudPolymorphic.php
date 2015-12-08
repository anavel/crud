<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field;
use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MiniCrudPolymorphic extends MiniCrud
{
    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\MorphMany',
        'Illuminate\Database\Eloquent\Relations\MorphOne'
    );

    protected function skipField($columnName, $key)
    {
        if ($columnName === $this->eloquentRelation->getPlainForeignKey()) {
            return true;
        }

        if ($columnName === $this->eloquentRelation->getPlainMorphType()) {
            return true;
        }

        if ($key === 'emptyResult' && ($columnName === $this->eloquentRelation->getParent()->getKeyName())) {
            return true;
        }
        return false;
    }

    protected function setKeys(Model $relationModel)
    {
        $relationModel->setAttribute($this->eloquentRelation->getForeignKey(), $this->relatedModel->id);
        $relationModel->setAttribute($this->eloquentRelation->getPlainMorphType(), $this->eloquentRelation->getMorphClass());
    }
}

