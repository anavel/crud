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
    protected $model;

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

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setModelFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function getForm($action)
    {
        $form = $this->factory->get('form', []);

        $form->attr([
            'action'  => $action,
            'enctype' => 'multipart/form-data',
            'method'  => 'post',
            'class'   => 'form-horizontal'
        ]);

        $formFields = array();
        foreach ($this->fields as $field) {
            $formFields[$field->name()] = $this->getFormField($field);
        }
        $form->add($formFields);

        return $form;
    }

    protected function getFormField(Field $modelField)
    {
        if (! array_key_exists($modelField->type()->getName(), $this->databaseTypeToFormType)) {
            throw new \Exception("No form type found for database type ".$modelField->type()->getName());
        }

        $formField = $this->factory->get($this->databaseTypeToFormType[$modelField->type()->getName()], []);

        $formField->class('form-control')
            ->label($modelField->presentation())
            ->placeholder($modelField->presentation());

        if ($this->model) {
            $formField->value($this->model->getAttribute($modelField->name()));
        }

        return $formField;
    }
}
