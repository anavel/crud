<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Contracts\Abstractor\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class MiniCrudSingle extends Relation
{
    use CheckRelationCompatibility;

    protected $compatibleEloquentRelations = [
        'Illuminate\Database\Eloquent\Relations\MorphOne',
    ];

    public function setup()
    {
        $this->checkRelationCompatibility();
    }

    /**
     * @return array
     */
    public function getEditFields($arrayKey = null)
    {
        if (empty($arrayKey)) {
            $arrayKey = $this->name;
        }

        $fields = [];

        $columns = $this->modelAbstractor->getColumns('edit');

        /** @var Model $result */
        $result = $this->eloquentRelation->getResults();

        $readOnly = [
            Model::CREATED_AT,
            Model::UPDATED_AT,
            $this->eloquentRelation->getPlainForeignKey(),
            $this->eloquentRelation->getPlainMorphType(),
            $this->eloquentRelation->getParent()->getKeyName(),
        ];

        $this->readConfig('edit');

        if (!empty($columns)) {
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
                ->setConfig($config)
                ->get();
            $fields[$arrayKey]['__delete'] = $field;

            foreach ($columns as $columnName => $column) {
                if (in_array($columnName, $readOnly, true)) {
                    continue;
                }

                $formType = null;

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

                if (!empty($result->id)) {
                    $field->setValue($result->getAttribute($columnName));
                }

                $fields[$arrayKey][$columnName] = $field;
            }
        }

        $fields = $this->addSecondaryRelationFields($fields);

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
            $currentRelation = $this->eloquentRelation->getResults();
            if (!empty($currentRelation)) {
                $relationModel = $currentRelation;
            } else {
                $relationModel = $this->eloquentRelation->getRelated()->newInstance();
            }

            $this->setKeys($relationModel);

            $shouldBeSkipped = true;
            foreach ($relationArray as $fieldKey => $fieldValue) {
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
            }
        }
    }

    protected function setKeys(Model $relationModel)
    {
        $relationModel->setAttribute($this->eloquentRelation->getForeignKey(), $this->relatedModel->id);
        $relationModel->setAttribute($this->eloquentRelation->getPlainMorphType(),
            $this->eloquentRelation->getMorphClass());
    }

    /**
     * @return string
     */
    public function getDisplayType()
    {
        return self::DISPLAY_TYPE_TAB;
    }
}
