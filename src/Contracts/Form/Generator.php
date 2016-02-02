<?php
namespace Anavel\Crud\Contracts\Form;

use Illuminate\Support\Collection;

interface Generator
{
    public function setModelFields(array $fields);

    public function addModelFields(array $fields);

    public function getForm($action);

    public function getValidationRules();

    /**
     * @param Collection $relations collection of Anavel\Crud\Contracts\Abstractor\Relation
     * @return void
     */
    public function setModelRelations(Collection $relations);

    /**
     * @param Collection $relations collection of Anavel\Crud\Contracts\Abstractor\Relation
     * @return void
     */
    public function setRelatedModelFields(Collection $relations);
}
