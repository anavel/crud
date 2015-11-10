<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

class Translation extends Relation
{
    protected $langs = ['en', 'es', 'gl']; //TODO get from config

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
