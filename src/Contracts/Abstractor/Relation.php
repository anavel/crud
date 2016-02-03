<?php
namespace Anavel\Crud\Contracts\Abstractor;

use Anavel\Crud\Abstractor\Exceptions\RelationException;
use Illuminate\Http\Request;

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
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request);

    /**
     * @return string
     */
    public function getDisplayType();
}
