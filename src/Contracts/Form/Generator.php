<?php
namespace Anavel\Crud\Contracts\Form;

interface Generator
{
    public function setModelFields(array $fields);

    public function addModelFields(array $fields);

    public function getForm($action);

    public function getValidationRules();

    /**
     * @param array $relations array of  Anavel\Crud\Contracts\Abstractor\Relation
     * @return void
     */
    public function setModelRelations(array $relations);

    /**
     * @param array $relations array of  Anavel\Crud\Contracts\Abstractor\Relation
     * @return void
     */
    public function setRelatedModelFields(array $relations);
}
