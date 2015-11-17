<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Model;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field;
use ANavallaSuiza\Crudoado\Repository\Criteria\InArrayCriteria;
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
                if ($this->eloquentRelation->getPlainForeignKey() === $field->getName() ) {
                    /** @var Field $foreignKeyField */
                    $foreignKeyField = clone $field;
                    $foreignKeyField->setName("{$this->name}[{$foreignKeyField->getName()}][]");
                    $foreignKeyField->setOptions($options);
                    $foreignKeyField->setCustomFormType('select');
                    $select[] = $foreignKeyField;
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
        if (! empty($selectArray = $request->input($this->name))) {
            /** @var \ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository $repo */
            $repo = $this->modelManager->getRepository(get_class($this->eloquentRelation->getRelated()));

            $results = $repo->pushCriteria(new InArrayCriteria($this->eloquentRelation->getRelated()->getKey(), $selectArray[$this->eloquentRelation->getPlainForeignKey()]))->all();

            $keyName = $this->eloquentRelation->getPlainForeignKey();
            foreach ($results as $result) {
                $result->$keyName = $this->relatedModel->getKey();
                $result->save();
            }
        }
    }
}
