<?php

namespace Anavel\Crud\Contracts\Abstractor;

use Doctrine\DBAL\Schema\Column;

interface FieldFactory
{
    /**
     * @param Column $column
     */
    public function setColumn(Column $column);

    /**
     * Field config.
     *
     * @param array $config
     */
    public function setConfig(array $config);

    /**
     * Factory method to create field instances.
     *
     * @throws \Exception
     *
     * @return \Anavel\Crud\Abstractor\Eloquent\Field
     */
    public function get();
}
