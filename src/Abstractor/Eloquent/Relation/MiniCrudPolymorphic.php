<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use App;
use Illuminate\Database\Eloquent\Model;

class MiniCrudPolymorphic extends MiniCrud
{
    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\MorphMany'
    );

    protected function skipField($columnName, $key)
    {
        if ($columnName === $this->eloquentRelation->getPlainForeignKey()) {
            return true;
        }

        if ($columnName === $this->eloquentRelation->getPlainMorphType()) {
            return true;
        }

        $foreignKeys = $this->dbal->getTableForeignKeys();
        $foreignKeysName = [];

        foreach ($foreignKeys as $foreignKey) {
            foreach ($foreignKey->getColumns() as $foreignColumnName) {
                $foreignKeysName[] = $foreignColumnName;
            }
        }

        if (in_array($columnName, $foreignKeysName)) {
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

