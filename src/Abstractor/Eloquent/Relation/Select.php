<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Select extends Relation
{
    use CheckRelationCompatibility;

    protected $compatibleEloquentRelations = [
        'Illuminate\Database\Eloquent\Relations\BelongsTo',
    ];

    public function setup()
    {
        $this->checkRelationCompatibility();
        $this->checkDisplayConfig();
    }

    /**
     * @param string|null $arrayKey
     *
     * @return array
     */
    public function getEditFields($arrayKey = 'main')
    {
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $dbal = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        $column = $this->getColumn($dbal);

        $repo = $this->modelManager->getRepository(get_class($this->eloquentRelation->getRelated()));

        $results = $repo->all();

        $options = ['' => ''];

        $this->readConfig('edit');

        foreach ($results as $result) {
            $options[$result->getKey()] = $this->setDisplay($result);
        }

        $config = $this->getConfig();

        $config = $this->setConfig($config, $column->getName());

        $field = $this->fieldFactory
            ->setColumn($column)
            ->setConfig($config)
            ->get();

        $field->setOptions($options);

        $field = $this->setFieldValue($field);

        $select = $this->addToArray($arrayKey, $field);

        return $select;
    }

    protected function addToArray($arrayKey, $field)
    {
        $select = [];
        $select[$arrayKey][] = $field;

        return $select;
    }

    /**
     * @param array|null $relationArray
     *
     * @return mixed
     */
    public function persist(array $relationArray = null, Request $request)
    {
        //
    }

    /**
     * @param Model $result
     *
     * @return string
     */
    protected function setDisplay($result)
    {
        if (is_array($this->config['display'])) {
            $displayString = '';
            foreach ($this->config['display'] as $key => $display) {
                if ($key !== 0) {
                    $displayString .= ' | ';
                }
                $displayString .= $result->getAttribute($display);
            }

            return $displayString;
        }

        return $result->getAttribute($this->config['display']);
    }

    /**
     * @return array
     */
    protected function getConfig()
    {
        return [
            'name'         => $this->eloquentRelation->getForeignKey(),
            'presentation' => $this->getPresentation(),
            'form_type'    => 'select',
            'validation'   => null,
            'functions'    => null,
        ];
    }

    protected function setFieldValue($field)
    {
        $results = $this->eloquentRelation->getResults();

        if (!empty($results)) {
            $field->setValue($results->getKey());
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
        return $dbal->getTableColumn($this->eloquentRelation->getOtherKey());
    }

    /**
     * @return string
     */
    public function getDisplayType()
    {
        return self::DISPLAY_TYPE_INLINE;
    }
}
