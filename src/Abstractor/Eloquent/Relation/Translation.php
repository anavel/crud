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
        $fields = [];

//        $relationName = $this->name;

//        dd($this->relatedModel->translations());
//        dd($this->modelManager->getAbstractionLayer($this->relatedModel->$relationName()));

//        $generator = \App::make('ANavallaSuiza\Crudoado\Contracts\Form\Generator');

//        foreach ($this->langs as $lang) {

//        }
        return $fields;
    }
}
