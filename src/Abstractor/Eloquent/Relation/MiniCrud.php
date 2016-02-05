<?php
namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Contracts\Abstractor\Field;
use App;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MiniCrud extends Relation
{
    use CheckRelationCompatibility;

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
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $dbal = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        $fields = [];

        if(empty($arrayKey)) {
            $arrayKey = $this->name;
        }

        $columns = $dbal->getTableColumns();

        /** @var Collection $results */
        $results = $this->eloquentRelation->getResults();

        $results->put('emptyResult', '');
        if (! empty($columns)) {
            $readOnly = [Model::CREATED_AT, Model::UPDATED_AT];
            foreach ($results as $key => $result) {
                foreach ($columns as $columnName => $column) {
                    if (in_array($columnName, $readOnly, true)) {
                        continue;
                    }
                    if ($this->skipField($columnName, $key)) {
                        continue;
                    }

                    $index = $key === 'emptyResult' ? 0 : $result->id;

                    $formType = null;
                    if ($key !== 'emptyResult' && ($columnName === $this->eloquentRelation->getParent()->getKeyName())) {
                        $formType = 'hidden';
                    }

                    $config = [
                        'name'         => $index . '[' . $columnName . ']',
                        'presentation' => $this->name . ' ' . ucfirst(transcrud($columnName)) . ' [' . $index . ']',
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

                    if ($key !== 'emptyResult') {
                        $field->setValue($result->getAttribute($columnName));
                    }

                    $fields[$arrayKey][] = $field;
                }

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
            $currentRelations = $this->eloquentRelation->get()->keyBy($this->eloquentRelation->getParent()->getKeyName());
            foreach ($relationArray as $relation) {
                if (! empty($relation[$this->eloquentRelation->getParent()->getKeyName()])
                    && ($currentRelations->has($relation[$this->eloquentRelation->getParent()->getKeyName()]))
                ) {
                    $relationModel = $currentRelations->get($relation[$this->eloquentRelation->getParent()->getKeyName()]);
                } else {
                    $relationModel = $this->eloquentRelation->getRelated()->newInstance();
                }

                $this->setKeys($relationModel);

                $shouldBeSkipped = true;
                foreach ($relation as $fieldKey => $fieldValue) {
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
}

