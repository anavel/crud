<?php
namespace ANavallaSuiza\Crudoado\Contracts\Abstractor;

use Doctrine\DBAL\Schema\Column;

interface FieldFactory
{
    /**
     *
     * @param Column $column
     */
    public function setColumn(Column $column);

    /**
     * Relation config
     *
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * Factory method to create field instances.
     *
     * @param string $name The name of the field
     *
     * @return \ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Relation
     *
     * @throws \Exception
     */
    public function get($name);
}
