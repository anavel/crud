<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface Model
{
    public function setSlug($slug);

    public function setName($name);

    public function setInstance($instance);

    public function getSlug();

    public function getName();

    public function getModel();

    public function getInstance();

    /**
     * @return boolean
     */
    public function isSoftDeletes();

    /**
     * @return array
     */
    public function getListFields();

    /**
     * @return array
     */
    public function getDetailFields();

    /**
     * @return array
     */
    public function getEditFields();

    /**
     * @return array
     */
    public function getRelations();
}
