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
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $dbal = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        $column = $dbal->getTableColumn($this->eloquentRelation->getPlainForeignKey());

        $repo = $this->modelManager->getRepository(get_class($this->eloquentRelation->getRelated()));

        $select = [];

        $results = $repo->all();

        $options = [];

        foreach ($results as $result) {
            $options[$result->getKey()] = $result->getAttribute($this->config['display']);
        }

        $config = [
            'name' => $this->name.'[]',
            'presentation' => $this->getPresentation(),
            'form_type' => 'select',
            'attr' => [
                'multiple' => true
            ],
            'no_validate' => true,
            'validation' => null,
            'functions' => null
        ];

        $field = $this->fieldFactory
            ->setColumn($column)
            ->setConfig($config)
            ->get();

        $field->setOptions($options);

        $results = $this->eloquentRelation->getResults();

        if (! $results->isEmpty()) {
            $values = [];

            foreach ($results as $result) {
                $values[] = $result->getKey();
            }

            $field->setValue($values);
        }

        $select[] = $field;

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

            $relationName = $this->name;
            $relatedKeyName = $this->eloquentRelation->getRelated()->getKeyName();
            $alreadyAssociated = $this->relatedModel->$relationName;

            $results = $repo->pushCriteria(
                new InArrayCriteria($relatedKeyName, $selectArray)
            )->all();

            $missing = $alreadyAssociated->diff($results);


            $keyName = $this->eloquentRelation->getPlainForeignKey();
            foreach ($results as $result) {
                $result->$keyName = $this->relatedModel->getKey();
                $result->save();
            }

            if (! $missing->isEmpty()) {
                foreach ($missing as $result) {
                    $result->$keyName = null;
                    $result->save();
                }
            }
        }
    }
}
