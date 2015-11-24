<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

use ANavallaSuiza\Crudoado\Abstractor\Exceptions\RelationException;
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
    public function getEditFields();

    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request);
}
