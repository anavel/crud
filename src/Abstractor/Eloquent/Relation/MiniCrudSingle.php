<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Contracts\Abstractor\Field;
use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MiniCrudSingle extends Relation
{
    use CheckRelationCompatibility;

    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\MorphOne'
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
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $dbal = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        if(empty($arrayKey)) {
            $arrayKey = $this->name;
        }

        $fields = [];

        $columns = $dbal->getTableColumns();

        /** @var Model $result */
        $result = $this->eloquentRelation->getResults();

        $readOnly = [
            Model::CREATED_AT,
            Model::UPDATED_AT,
            $this->eloquentRelation->getPlainForeignKey(),
            $this->eloquentRelation->getPlainMorphType(),
            $this->eloquentRelation->getParent()->getKeyName()
        ];


        if (! empty($columns)) {
            foreach ($columns as $columnName => $column) {
                if (in_array($columnName, $readOnly, true)) {
                    continue;
                }

                $formType = null;

                $config = [
                    'name'         => $columnName,
                    'presentation' => $this->name . ' ' . ucfirst(transcrud($columnName)),
                    'form_type'    => $formType,
                    'no_validate'  => true,
                    'validation'   => null,
                    'functions'    => null
                ];

                /** @var Field $field */
                $field = $this->fieldFactory
                    ->setColumn($column)
                    ->setConfig($config)
                    ->get();

                if (! empty($result->id)) {
                    $field->setValue($result->getAttribute($columnName));
                }

                $fields[$arrayKey][] = $field;
            }
        }

        $fields = $this->addSecondaryRelationFields($fields);


        return $fields;
    }

    /**
     * @param array|null $relationArray
     * @return mixed
     */
    public function persist(array $relationArray = null)
    {
        if (! empty($relationArray)) {
            $currentRelation = $this->eloquentRelation->getResults();
            if (! empty($currentRelation)) {
                $relationModel = $currentRelation;
            } else {
                $relationModel = $this->eloquentRelation->getRelated()->newInstance();
            }

            $this->setKeys($relationModel);

            $shouldBeSkipped = true;
            foreach ($relationArray as $fieldKey => $fieldValue) {
                if ($shouldBeSkipped) {
                    $shouldBeSkipped = ($shouldBeSkipped === ($fieldValue === ''));
                }
                $relationModel->setAttribute($fieldKey, $fieldValue);
            }

            if (! $shouldBeSkipped) {
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

