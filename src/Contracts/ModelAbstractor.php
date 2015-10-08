<?php
namespace ANavallaSuiza\Crudoado\Contracts;

interface ModelAbstractor
{
    public function loadBySlug($slug);

    public function loadByName($name);

    public function getSlug();

    public function getName();

    public function getModel();

    public function isSoftDeletes();

    public function getListFields();

    public function getDetailFields();
}
