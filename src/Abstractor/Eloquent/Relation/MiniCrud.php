<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Abstractor\Eloquent\Traits\HandleFiles;
use Anavel\Crud\Contracts\Abstractor\Field;
use Anavel\Crud\Contracts\Abstractor\Relation as RelationContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MiniCrud extends Relation
{
    use CheckRelationCompatibility;
    use HandleFiles;

    /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
    protected $dbal;

    /** @var Collection */
    protected $results;

    protected $compatibleEloquentRelations = [
        'Illuminate\Database\Eloquent\Relations\HasMany',
    ];

    /**
     * @return Collection
     */
    protected function getResults()
    {
        if (empty($this->results)) {
            return $this->results = $this->eloquentRelation->getResults();
        }

        return $this->results;
    }

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

        $fieldsBase = $this->getEditFieldsBase();

        /** @var Collection $results */
        $results = $this->getResults();

        $results->put('emptyResult', '');
        if (!empty($fieldsBase)) {
            foreach ($results as $key => $result) {
                $tempFields = [];
                $index = $key === 'emptyResult' ? 0 : $result->id;

                foreach ($fieldsBase as $columnName => $fieldBase) {
                    $field = clone $fieldBase;
                    if ($this->skipField($columnName, $key)) {
                        continue;
                    }

                    if ($columnName != '__delete') {
                        if ($key !== 'emptyResult') {
                            $field->setValue($result->getAttribute($columnName));
                        }
                    } elseif ($key === 'emptyResult') {
                        continue;
                    }
                    $tempFields[$columnName] = $field;
                }

                $relationModel = $this->eloquentRelation->getRelated()->newInstance();
                if (!empty($result)) {
                    $relationModel = $result;
                }

                $this->modelAbstractor->setInstance($relationModel);
                $secondaryRelations = $this->getSecondaryRelations();

                if (!empty($secondaryRelations)) {
                    foreach ($secondaryRelations as $secondaryRelationKey => $secondaryRelation) {
                        foreach ($secondaryRelation->getEditFields($secondaryRelationKey) as $editGroupName => $editGroup) {
                            if ($secondaryRelation->getType() === 'Anavel\Crud\Abstractor\Eloquent\Relation\Select') {
                                $tempFields[$editGroup[key($editGroup)]->getName()] = $editGroup[key($editGroup)];
                            } else {
                                $tempFields[$editGroupName] = $editGroup;
                            }
                        }
                    }
                }

                $fields[$arrayKey][$index] = $tempFields;
            }
        }

        return $fields;
    }

    public function getEditFieldsBase()
    {
        $fields = [];
        $columns = $this->modelAbstractor->getColumns('edit');
        $this->readConfig('edit');

        if (!empty($columns)) {
            $readOnly = [Model::CREATED_AT, Model::UPDATED_AT, 'deleted_at'];

            //Add field for model deletion
            $config = [
                'name'         => '__delete',
                'presentation' => 'Delete',
                'form_type'    => 'checkbox',
                'no_validate'  => true,
                'validation'   => null,
                'functions'    => null,
            ];

            /** @var Field $field */
            $field = $this->fieldFactory
                ->setColumn($columns[key($columns)]) //Set any column, we are not really using it
                ->setConfig($config)
                ->get();
            $fields['__delete'] = $field;

            foreach ($columns as $columnName => $column) {
                if (in_array($columnName, $readOnly, true)) {
                    continue;
                }

                $formType = null;
                if ($columnName === $this->eloquentRelation->getParent()->getKeyName()) {
                    $formType = 'hidden';
                }

                $config = [
                    'name'         => $columnName,
                    'presentation' => $this->name.' '.ucfirst(transcrud($columnName)),
                    'form_type'    => $formType,
                    'no_validate'  => true,
                    'validation'   => null,
                    'functions'    => null,
                ];

                $config = $this->setConfig($config, $columnName);

                /** @var Field $field */
                $field = $this->fieldFactory
                    ->setColumn($column)
                    ->setConfig($config)
                    ->get();

                $fields[$columnName] = $field;

                if (!empty($config['form_type']) && $config['form_type'] === 'file') {
                    $field = $this->fieldFactory
                        ->setColumn($column)
                        ->setConfig([
                            'name'         => $columnName.'__delete',
                            'presentation' => null,
                            'form_type'    => 'checkbox',
                            'no_validate'  => true,
                            'validation'   => null,
                            'functions'    => null,
                        ])
                        ->get();
                    $fields[$columnName.'__delete'] = $field;
                }
            }
        }

        return $fields;
    }

    /**
     * @param array|null $relationArray
     *
     * @return mixed
     */
    public function persist(array $relationArray = null, Request $request)
    {
        if (!empty($relationArray)) {
            $keyName = $this->eloquentRelation->getParent()->getKeyName();
            $currentRelations = $this->getResults()->keyBy($keyName);

            $this->readConfig('edit');
            $fieldsBase = $this->getEditFieldsBase();

            foreach ($relationArray as $relationIndex => &$relation) {
                if (!empty($relation[$keyName])
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

                $skip = null;
                foreach ($fieldsBase as $fieldBaseKey => $field) {
                    $fieldName = $field->getName();

                    if (get_class($field->getFormField()) === \FormManager\Fields\File::class) {
                        $handleResult = $this->handleField($request, $relationModel, $fieldsBase,
                            $this->name.".$relationIndex", $fieldName, $this->modelAbstractor->mustDeleteFilesInFilesystem());
                        if (!empty($handleResult['skip'])) {
                            $skip = $handleResult['skip'];
                            unset($relationArray[$relationIndex][$skip]);
                        }
                        if (!empty($handleResult['requestValue'])) {
                            $relationArray[$relationIndex][$fieldName] = $handleResult['requestValue'];
                        }
                    }

                    if ($fieldName !== '__delete' && ($fieldName != $skip && (get_class($field->getFormField()) === \FormManager\Fields\Checkbox::class))) {
                        if (empty($relationArray[$relationIndex][$fieldName])) {
                            // Unchecked checkboxes are not sent, so we force setting them to false
                            $relationModel->setAttribute($fieldName, null);
                        } else {
                            $relationArray[$relationIndex][$fieldName] = true;
                        }
                    }
                }

                foreach ($relation as $fieldKey => $fieldValue) {
                    if ($secondaryRelations->has($fieldKey)) {
                        $delayedRelations->put($fieldKey, $fieldValue);
                        continue;
                    }

                    // This field can only come from existing models
                    if ($fieldKey === '__delete') {
                        $relationModel->delete();
                        $shouldBeSkipped = true;
                        break;
                    }

                    if ($shouldBeSkipped) {
                        $shouldBeSkipped = ($shouldBeSkipped === ($fieldValue === ''));
                    }

                    $relationModel->setAttribute($fieldKey, $fieldValue);
                }

                if (!$shouldBeSkipped) {
                    $relationModel->save();

                    if (!$delayedRelations->isEmpty()) {
                        foreach ($delayedRelations as $relationKey => $delayedRelation) {
                            /** @var RelationContract $secondaryRelation */
                            $secondaryRelation = $secondaryRelations->get($relationKey);

                            $secondaryRelation->persist($delayedRelation, $request);
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
     *
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
            }
        }
        foreach ($fields[$this->name] as $groupKey => $mainFields) {
            $combinedFields = array_merge($mainFields, $tempFields);
            $fields[$this->name][$groupKey] = $combinedFields;
        }

        return $fields;
    }
}
