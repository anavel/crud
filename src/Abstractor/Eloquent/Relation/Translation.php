<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model;
use App;

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
        /** @var \ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory $modelFactory */
        $modelFactory = App::make('ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory');

        $modelAbstractor = $modelFactory->getByClassName(get_class($this->eloquentRelation->getRelated()));

        $fields = $modelAbstractor->getEditFields();

        $translationFields = [];
        if (! empty($fields)) {
            foreach ($this->langs as $key => $lang) {
                foreach ($fields as $field) {
                    if (strpos($this->eloquentRelation->getForeignKey(), $field->getName()) === false) {
                        $langField = clone $field;
                        if ($langField->getName() == 'locale') {
                            $langField->setCustomFormType('hidden');
                        }
                        $langField->setName("{$this->name}[$key][{$langField->getName()}]");
                        $translationFields[] = $langField;
                    }
                }
            }
        }
        return $translationFields;
    }
}
