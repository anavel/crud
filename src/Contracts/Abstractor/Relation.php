<?php
namespace Anavel\Crud\Contracts\Abstractor;

use Anavel\Crud\Abstractor\Exceptions\RelationException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

interface Relation
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPresentation();

    /**
     * @return string
     */
    public function getType();

    /**
     * @throws RelationException
     */
    public function setup();

    /**
     * @return array
     */
    public function getEditFields($arrayKey = null);

    /**
     * @param array $fields
     * @return array
     */
    public function addSecondaryRelationFields(array $fields);

    /**
     * @param array|null $relationArray
     * @return mixed
     */
    public function persist(array $relationArray = null, Request $request);

    /**
     * @return string
     */
    public function getDisplayType();

    /**
     * @return string
     */
    public function getDisplay();

    /**
     * @return Collection
     */
    public function getSecondaryRelations();

    /**
     * @param Model $relatedModel
     * @return Relation
     */
    public function setRelatedModel(\Illuminate\Database\Eloquent\Model $model);
}
