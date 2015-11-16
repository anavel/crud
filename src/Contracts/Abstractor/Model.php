<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

use FormManager\ElementInterface;
use Illuminate\Http\Request;

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

    /**
     * @param string $action
     * @return ElementInterface
     */
    public function getForm($action);

    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request);

    /**
     * @return array
     */
    public function getValidationRules();
}
