<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface ModelFactory
{
    /**
     *
     * @return Model
     */
    public function getBySlug($slug, $id = null);

    /**
     *
     * @return Model
     */
    public function getByName($name, $id = null);
}
