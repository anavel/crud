<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field;
use App;
use Illuminate\Http\Request;

class Translation extends Relation
{
    use CheckRelationCompatibility;

    protected $langs = ['en', 'es', 'gl']; //TODO get from config

    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\HasMany'
    );

    public function setup()
    {
        $this->checkRelationCompatibility();
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
                        /** @var Field $langField */
                        $langField = clone $field;
                        if ($langField->getName() == 'locale') {
                            $langField->setCustomFormType('hidden');
                            $langField->setValue($lang);
                        }
                        $langField->setName("{$this->name}[$key][{$langField->getName()}]");
                        $translationFields[] = $langField;
                    }
                }
            }
        }
        return $translationFields;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request)
    {
        if (! empty($translationsArray = $request->input($this->name))) {
            foreach ($translationsArray as $translation) {
                $translationModel = clone $this->eloquentRelation->getRelated();
                $translationModel->setAttribute($this->eloquentRelation->getForeignKey(), $this->relatedModel->id);

                foreach ($translation as $fieldKey => $fieldValue) {
                    $translationModel->setAttribute($fieldKey, $fieldValue);
                }


                $translationModel->save();
            }
        }
    }
}
