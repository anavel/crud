<?php
namespace ANavallaSuiza\Crudoado\Http\Form;

use ANavallaSuiza\Crudoado\Contracts\Form\Generator as GeneratorContract;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field;
use Doctrine\DBAL\Types\Type as DbalType;
use FormManager\FactoryInterface;

class Generator implements GeneratorContract
{
    /**
     *
     */
    protected $factory;

    /**
     *
     */
    protected $fields;

    /**
     *
     */
    protected $databaseTypeToFormType = array(
        DbalType::INTEGER  => 'number',
        DbalType::STRING   => 'text',
        DbalType::TEXT     => 'textarea',
        DbalType::BOOLEAN  => 'checkbox',
        DbalType::DATE     => 'date',
        DbalType::TIME     => 'time',
        DbalType::DATETIME => 'datetime',
        DbalType::DECIMAL  => 'number',
        DbalType::FLOAT    => 'number',

    );

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
        $this->fields = array();
    }

    public function setModelFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getForm($action)
    {
        $form = $this->factory->get('form');

        $form->attr([
            'action' => $action,
            'enctype' => 'multipart/form-data',
            'method' => 'post'
        ]);

        foreach ($this->fields as $field) {
            $form->add($this->getFormField($field));
        }

        return $form;
    }

    protected function getFormField(Field $field)
    {

    }
}
