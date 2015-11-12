<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

class Translation extends Relation
{
    protected $langs = ['en', 'es', 'gl']; //TODO get from config

    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\HasMany'
    );

    public function checkEloquentRelationCompatibility()
    {
        if (! in_array(get_class($this->eloquentRelation), $this->compatibleEloquentRelations)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        $modelAbstractor = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        $fields = $modelAbstractor->getEditFields();

        return $fields;
    }
}
