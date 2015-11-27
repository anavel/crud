<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field;
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
    public function getEditFields()
    {
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $dbal = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        $fields = [];

        $columns = $dbal->getTableColumns();

        /** @var Collection $results */
        $results = $this->eloquentRelation->getResults();

        $results->put('emptyResult', '');
        if (! empty($columns)) {
            foreach ($results as $key => $result) {
                foreach ($columns as $columnName => $column) {
                    if ($this->skipField($columnName, $key)) {
                        continue;
                    }

                    $index = $key === 'emptyResult' ? 0 : $result->id;

                    $formType = null;
                    if ($key !== 'emptyResult' && ($columnName === $this->eloquentRelation->getParent()->getKeyName())) {
                        $formType = 'hidden';
                    }

                    $config = [
                        'name'         => $this->name . '[' . $index . '][' . $columnName . ']',
                        'presentation' => ucfirst($columnName) . ' [' . $index . ']',
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

                    $fields[] = $field;
                }

            }
        }


        return $fields;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request)
    {
        if (! empty($relationArray = $request->input($this->name))) {
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
                    $relationModel->setAttribute($fieldKey, $fieldValue);
                    if (! $shouldBeSkipped) {
                        break;
                    }
                    $shouldBeSkipped = ($shouldBeSkipped === ($fieldValue === ''));
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
}

