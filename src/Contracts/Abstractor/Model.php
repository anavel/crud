<?php
namespace Anavel\Crud\Contracts\Abstractor;

use FormManager\ElementInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
     * @return array
     */
    public function getConfig();

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
     * @param bool $withForeignKeys
     * @param string $arrayKey
     * @return array
     */
    public function getEditFields($withForeignKeys = false, $arrayKey = 'main');

    /**
     * @return Collection
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

    /**
     * @param string $action
     * @param bool|false $withForeignKeys
     * @return array
     */
    public function getColumns($action, $withForeignKeys = false);
}
