<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field;
use App;
use Illuminate\Http\Request;

class Select extends Relation
{
    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\BelongsTo'
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

        $options = [];

        foreach ($results as $result) {
            $fieldName = $this->config['display'];
            $options[$result->getKey()] = $result->getAttribute($fieldName);
        }

        if (! empty($fields)) {
            foreach ($fields as $field) {
                if ($this->eloquentRelation->getPlainForeignKey() === $field->getName()) {
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
        // TODO: Implement persist() method.
    }
}
