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
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $dbal = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        $column = $dbal->getTableColumn($this->eloquentRelation->getOtherKey());

        $repo = $this->modelManager->getRepository(get_class($this->eloquentRelation->getRelated()));

        $select = [];

        $results = $repo->all();

        $options = [];

        foreach ($results as $result) {
            $fieldName = $this->config['display'];
            $options[$result->getKey()] = $result->getAttribute($fieldName);
        }

        $config = [
            'name' => $this->eloquentRelation->getForeignKey(),
            'presentation' => $this->presentation,
            'form_type' => 'select',
            'validation' => null,
            'functions' => null
        ];

        $field = $this->fieldFactory
            ->setColumn($column)
            ->setConfig($config)
            ->get();

        $select[] = $field;

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
