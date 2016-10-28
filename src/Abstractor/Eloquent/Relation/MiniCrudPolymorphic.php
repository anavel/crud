<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Illuminate\Database\Eloquent\Model;

class MiniCrudPolymorphic extends MiniCrud
{
    protected $compatibleEloquentRelations = [
        'Illuminate\Database\Eloquent\Relations\MorphMany',
    ];

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

    /**
     * @return string
     */
    public function getDisplayType()
    {
        return self::DISPLAY_TYPE_TAB;
    }
}
