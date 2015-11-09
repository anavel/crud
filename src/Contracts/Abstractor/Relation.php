<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

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
}
