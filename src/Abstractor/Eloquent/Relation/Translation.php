<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model;

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
        $modelAbstractor = new Model(
            get_class($this->eloquentRelation->getRelated()),
            $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated())),
            \App::make('ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory', array($this->modelManager))
        );

        $fields = $modelAbstractor->getEditFields();

        if (! empty($fields)) {
            foreach ($fields as $field) {
                $field->setName("{$this->name}[][{$field->getName()}]");
            }
        }

        return $fields;
    }
}
