<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field as FieldAbstractorContract;
use Doctrine\DBAL\Schema\Column;

class Field implements FieldAbstractorContract
{
    protected $dbal;
    protected $name;
    protected $presentation;

    public function __construct(Column $column, $name, $presentation = null)
    {
        $this->dbal = $column;
        $this->name = $name;
        $this->presentation = $presentation;
    }

    public function name()
    {
        return $this->name;
    }

    public function presentation()
    {
        return $this->presentation ? : ucfirst(str_replace('_', ' ', $this->name));
    }

    public function type()
    {
        return $this->dbal->getType();
    }

    public function getValidationRules()
    {
        $rules = array();

        if ($this->dbal->getNotNull()) {
            $rules[] = 'required';
        }

        return implode('|', $rules);
    }
}
