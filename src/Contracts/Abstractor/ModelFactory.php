<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface ModelFactory
{
    /**
     *
     * @return Model
     */
    public function getBySlug($slug);

    /**
     *
     * @return Model
     */
    public function getByName($name);
}
