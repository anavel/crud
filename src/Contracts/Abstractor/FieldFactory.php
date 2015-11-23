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
     * Field config
     *
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * Factory method to create field instances.
     *
     * @return \ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field
     *
     * @throws \Exception
     */
    public function get();
}
