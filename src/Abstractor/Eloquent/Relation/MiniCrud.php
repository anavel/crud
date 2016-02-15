<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Contracts\Abstractor\Field;
use Anavel\Crud\Contracts\Abstractor\Relation as RelationContract;
use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MiniCrud extends Relation
{
    use CheckRelationCompatibility;

    /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
    protected $dbal;

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
    public function getEditFields($arrayKey = null)
    {
        $fields = [];

        if (empty($arrayKey)) {
            $arrayKey = $this->name;
        }

        $columns = $this->modelAbstractor->getColumns('edit');

        $this->readConfig('edit');

        /** @var Collection $results */
        $results = $this->eloquentRelation->getResults();

        $results->put('emptyResult', '');
        if (! empty($columns)) {
            $readOnly = [Model::CREATED_AT, Model::UPDATED_AT];
            foreach ($results as $key => $result) {
                $tempFields = [];
                $index = $key === 'emptyResult' ? 0 : $result->id;
                foreach ($columns as $columnName => $column) {
                    if (in_array($columnName, $readOnly, true)) {
                        continue;
                    }
                    if ($this->skipField($columnName, $key)) {
                        continue;
                    }


                    $formType = null;
                    if ($key !== 'emptyResult' && ($columnName === $this->eloquentRelation->getParent()->getKeyName())) {
                        $formType = 'hidden';
                    }

                    $config = [
                        'name'         => $columnName,
                        'presentation' => $this->name . ' ' . ucfirst(transcrud($columnName)) . ' [' . $index . ']',
                        'form_type'    => $formType,
                        'no_validate'  => true,
                        'validation'   => null,
                        'functions'    => null
                    ];

                    $config = $this->setConfig($config, $columnName);

                    /** @var Field $field */
                    $field = $this->fieldFactory
                        ->setColumn($column)
                        ->setConfig($config)
                        ->get();

                    if ($key !== 'emptyResult') {
                        $field->setValue($result->getAttribute($columnName));
                    }
                    $tempFields[] = $field;
                }

                $relationModel = $this->eloquentRelation->getRelated()->newInstance();
                if (! empty($result)) {
                    $relationModel = $result;
                }
                $this->modelAbstractor->setInstance($relationModel);
                $secondaryRelations = $this->getSecondaryRelations();
                if (! empty($secondaryRelations)) {
                    foreach ($secondaryRelations as $secondaryRelationKey => $secondaryRelation) {
                        foreach ($secondaryRelation->getEditFields($secondaryRelationKey) as $editGroupName => $editGroup) {
                            if ($secondaryRelation->getType() === 'Anavel\Crud\Abstractor\Eloquent\Relation\Select') {
                                $tempFields[key($editGroup)] = $editGroup[key($editGroup)];
                            } else {
                                $tempFields[$editGroupName] = $editGroup;
                            }
                        };
                    }
                }

                $fields[$arrayKey][$index] = $tempFields;
            }
        }

//        $fields = $this->addSecondaryRelationFields($fields);

        return $fields;
    }

    /**
     * @param array|null $relationArray
     * @return mixed
     */
    public function persist(array $relationArray = null)
    {
        if (! empty($relationArray)) {
            $keyName = $this->eloquentRelation->getParent()->getKeyName();
            $currentRelations = $this->eloquentRelation->get()->keyBy($keyName);

            foreach ($relationArray as $relation) {
                if (! empty($relation[$keyName])
                    && ($currentRelations->has($relation[$keyName]))
                ) {
                    $relationModel = $currentRelations->get($relation[$keyName]);
                } else {
                    $relationModel = $this->eloquentRelation->getRelated()->newInstance();
                }

                $this->modelAbstractor->setInstance($relationModel);
                $secondaryRelations = $this->getSecondaryRelations();


                $this->setKeys($relationModel);

                $shouldBeSkipped = true;
                $delayedRelations = collect();


                foreach ($relation as $fieldKey => $fieldValue) {
                    if ($secondaryRelations->has($fieldKey)) {
                        $delayedRelations->put($fieldKey, $fieldValue);
                        continue;
                    }

                    if ($shouldBeSkipped) {
                        $shouldBeSkipped = ($shouldBeSkipped === ($fieldValue === ''));
                    }

                    $relationModel->setAttribute($fieldKey, $fieldValue);
                }

                if (! $shouldBeSkipped) {
                    $relationModel->save();

                    if (! $delayedRelations->isEmpty()) {
                        foreach ($delayedRelations as $relationKey => $delayedRelation) {
                            /** @var RelationContract $secondaryRelation */
                            $secondaryRelation = $secondaryRelations->get($relationKey);

                            $secondaryRelation->persist($delayedRelation);
                        }
                    }
                }
            }
        }
    }

    protected function setKeys(Model $relationModel)
    {
        $relationModel->setAttribute($this->eloquentRelation->getForeignKey(), $this->relatedModel->id);
    }

    protected function skipField($columnName, $key)
    {
        if ($columnName === $this->eloquentRelation->getPlainForeignKey()) {
            return true;
        }

        if ($key === 'emptyResult' && ($columnName === $this->eloquentRelation->getParent()->getKeyName())) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getDisplayType()
    {
        return self::DISPLAY_TYPE_TAB;
    }

    /**
     * @param array $fields
     * @return array
     */
    public function addSecondaryRelationFields(array $fields)
    {
        $tempFields = [];
        foreach ($this->getSecondaryRelations() as $relationKey => $relation) {
            foreach ($relation->getEditFields($relationKey) as $editGroupName => $editGroup) {
                if ($relation->getType() === 'Anavel\Crud\Abstractor\Eloquent\Relation\Select') {
                    $tempFields[key($editGroup)] = $editGroup[key($editGroup)];
                } else {
                    $tempFields[$editGroupName] = $editGroup;
                }
            };
        }
        foreach ($fields[$this->name] as $groupKey => $mainFields) {
            $combinedFields = array_merge($mainFields, $tempFields);
            $fields[$this->name][$groupKey] = $combinedFields;
        }

        return $fields;
    }
}

