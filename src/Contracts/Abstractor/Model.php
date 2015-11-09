<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

interface Model
{
    public function setSlug($slug);

    public function setName($name);

    public function getSlug();

    public function getName();

    public function getModel();

    public function isSoftDeletes();

    public function getListFields();

    public function getDetailFields();

    public function getEditFields();

    public function getEditRelations();
}
