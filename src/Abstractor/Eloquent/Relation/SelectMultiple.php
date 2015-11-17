<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field;
use App;
use Illuminate\Http\Request;

class SelectMultiple extends Relation
{
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

        $repo = $this->modelManager->getRepository(get_class($this->eloquentRelation->getRelated()));

        $select = [];

        $results = $repo->all();

        if ($results->isEmpty()) {
            return $select;
        }

        $options = [];

        foreach ($results as $result) {
            $fieldName = $this->config['display'];
            $options[$result->getKey()] = $result->$fieldName;
        }

        if (! empty($fields)) {
            foreach ($fields as $field) {
                if (strpos($this->eloquentRelation->getForeignKey(), $field->getName()) !== false) {
                    /** @var Field $primaryKeyField */
                    $primaryKeyField = clone $field;
                    $primaryKeyField->setName("{$this->name}[{$primaryKeyField->getName()}][]");
                    $primaryKeyField->setOptions($options);
                    $primaryKeyField->setCustomFormType('select');
                    $select[] = $primaryKeyField;
                }
            }
        }
        return $select;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request)
    {
//        if (! empty($translationsArray = $request->input($this->name))) {
//            foreach ($translationsArray as $translation) {
//                $translationModel = clone $this->eloquentRelation->getRelated();
//                $translationModel->setAttribute($this->eloquentRelation->getForeignKey(), $this->relatedModel->id);
//
//                foreach ($translation as $fieldKey => $fieldValue) {
//                    $translationModel->setAttribute($fieldKey, $fieldValue);
//                }
//
//
//                $translationModel->save();
//            }
//        }
    }
}
