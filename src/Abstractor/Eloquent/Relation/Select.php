<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use App;
use Illuminate\Http\Request;

class Select extends Relation
{
    use CheckRelationCompatibility, CheckRelationConfig;

    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\BelongsTo'
    );

    public function setup()
    {
        $this->checkRelationCompatibility();
        $this->checkDisplayConfig();
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

        $options = ['' => ''];

        foreach ($results as $result) {
            $options[$result->getKey()] = $result->getAttribute($this->config['display']);
        }

        $config = [
            'name' => $this->eloquentRelation->getForeignKey(),
            'presentation' => $this->getPresentation(),
            'form_type' => 'select',
            'validation' => null,
            'functions' => null
        ];

        $field = $this->fieldFactory
            ->setColumn($column)
            ->setConfig($config)
            ->get();

        $field->setOptions($options);

        $results = $this->eloquentRelation->getResults();

        if (! empty($results)) {
            $field->setValue($results->getKey());
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
        //
    }
}
