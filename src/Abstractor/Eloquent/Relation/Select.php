<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

class Select extends Relation
{
    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\BelongsTo'
    );

    public function checkEloquentRelationCompatibility()
    {
        if (! in_array(get_class($this->eloquentRelation), $this->compatibleEloquentRelations)) {
            return false;
        }

        return true;
    }
}
