<?php


namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits;


use ANavallaSuiza\Crudoado\Abstractor\Exceptions\RelationException;

trait CheckRelationCompatibility
{
    public function checkRelationCompatibility()
    {
        if (! in_array(get_class($this->eloquentRelation), $this->compatibleEloquentRelations)) {
            throw new RelationException(get_class($this->eloquentRelation)." eloquent relation is not compatible with ".$this->getType()." type");
        }
    }
}