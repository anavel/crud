<?php
namespace ANavallaSuiza\Crudoado\Http\Form;

use ANavallaSuiza\Crudoado\Contracts\Form\Generator as GeneratorContract;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field;
use Doctrine\DBAL\Types\Type as DbalType;
use FormManager\FactoryInterface;
use Request;

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
        if ($modelField->hasCustomFormType()) {
            if (! in_array($modelField->getCustomFieldType(), $this->databaseTypeToFormType)) {
                throw new \Exception("Unknown form type ".$modelField->getCustomFieldType());
            }

            $formFieldType = $modelField->getCustomFieldType();
        } else {
            if (! array_key_exists($modelField->type()->getName(), $this->databaseTypeToFormType)) {
                throw new \Exception("No form type found for database type ".$modelField->type()->getName());
            }

            $formFieldType = $this->databaseTypeToFormType[$modelField->type()->getName()];
        }

        $formField = $this->factory->get($formFieldType, []);

        $formField->class('form-control')
            ->label($modelField->presentation())
            ->placeholder($modelField->presentation());

        if ($formFieldType === 'textarea') {
            $formField->class('form-control '.config('crudoado.text_editor'));
        }

        if ($this->model) {
            $formField->val($this->model->getAttribute($modelField->name()));
        }

        if (Request::old($modelField->name())) {
            $formField->val(Request::old($modelField->name()));
        }

        return $formField;
    }

    public function getValidationRules()
    {
        $rules = array();

        foreach ($this->fields as $field) {
            $rules[$field->name()] = $field->getValidationRules();
        }

        return $rules;
    }
}
