<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Repository\Criteria\InArrayCriteria;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Http\Request;

class SelectMultiple extends Select
{
    use CheckRelationCompatibility;

    protected $compatibleEloquentRelations = [
        'Illuminate\Database\Eloquent\Relations\HasMany',
    ];

    public function setup()
    {
        $this->checkRelationCompatibility();
        $this->checkDisplayConfig();
    }

    /**
     * @param array|null $relationArray
     *
     * @return mixed
     */
    public function persist(array $relationArray = null, Request $request)
    {
        if (!empty($relationArray)) {
            /** @var \ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository $repo */
            $repo = $this->modelManager->getRepository(get_class($this->eloquentRelation->getRelated()));

            $relationName = $this->name;
            $relatedKeyName = $this->eloquentRelation->getRelated()->getKeyName();
            $alreadyAssociated = $this->relatedModel->$relationName;

            $search = [];
            if (!empty($relationArray[$relatedKeyName])) {
                $search = $relationArray[$relatedKeyName];
            }
            $results = $repo->pushCriteria(
                new InArrayCriteria($relatedKeyName, $search)
            )->all();

            $missing = $alreadyAssociated->diff($results);


            $keyName = $this->eloquentRelation->getPlainForeignKey();
            foreach ($results as $result) {
                $result->$keyName = $this->relatedModel->getKey();
                $result->save();
            }

            if (!$missing->isEmpty()) {
                foreach ($missing as $result) {
                    $result->$keyName = null;
                    $result->save();
                }
            }
        }
    }

    protected function addToArray($arrayKey, $field)
    {
        $select = [];
        if ($arrayKey == 'main') {
            $arrayKey = $this->name;
        }
        $select[$arrayKey][] = $field;

        return $select;
    }

    /**
     * @return array
     */
    protected function getConfig()
    {
        return [
            'name'         => $this->eloquentRelation->getRelated()->getKeyName(),
            'presentation' => $this->getPresentation(),
            'form_type'    => 'select',
            'attr'         => [
                'multiple' => true,
            ],
            'no_validate' => true,
            'validation'  => null,
            'functions'   => null,
        ];
    }

    protected function setFieldValue($field)
    {
        $results = $this->eloquentRelation->getResults();

        if (!$results->isEmpty()) {
            $values = [];

            foreach ($results as $result) {
                $values[] = $result->getKey();
            }

            $field->setValue($values);
        }

        return $field;
    }

    /**
     * @param \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal
     *
     * @return Column
     */
    protected function getColumn($dbal)
    {
        return $dbal->getTableColumn($this->eloquentRelation->getPlainForeignKey());
    }

    /**
     * @return string
     */
    public function getDisplayType()
    {
        return self::DISPLAY_TYPE_INLINE;
    }
}
