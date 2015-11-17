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
     * @var array
     */
    protected $fields;

    /**
     * @var array
     */
    protected $relations;

    /**
     * @var array
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
        'email',
        'password',
        'hidden',
        'select'
    );

    protected $relationsTypeToFormType = array();

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
        $this->fields = array();
    }

    public function setModelFields(array $fields)
    {
        $this->fields = $fields;
    }

    public function addModelFields(array $fields)
    {
        $this->fields = array_merge($this->fields, $fields);
    }

    public function setModelRelations(array $relations)
    {
        $this->relations = $relations;
    }

    public function setRelatedModelFields(array $relations)
    {
        $this->setModelRelations($relations);

        if (count($this->relations > 0)) {
            foreach ($this->relations as $relation) {
                $this->addModelFields($relation->getEditFields());
            }
        }
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
            $formFields[$field->getName()] = $this->getFormField($field);
        }
        $form->add($formFields);

        return $form;
    }

    protected function getFormField(Field $modelField)
    {
        if ($modelField->hasCustomFormType()) {
            if (! in_array($modelField->getCustomFormType(), $this->databaseTypeToFormType)) {
                throw new \Exception("Unknown form type ".$modelField->getCustomFormType());
            }

            $formFieldType = $modelField->getCustomFormType();
        } else {
            if (! array_key_exists($modelField->type()->getName(), $this->databaseTypeToFormType)) {
                throw new \Exception("No form type found for database type ".$modelField->type()->getName());
            }

            $formFieldType = $this->databaseTypeToFormType[$modelField->type()->getName()];
        }

        $formField = $this->factory->get($formFieldType, []);

        if ($formFieldType != 'hidden') {
            $formField->class('form-control')
                ->label($modelField->presentation())
                ->placeholder($modelField->presentation());
        }

        if ($formFieldType === 'textarea') {
            $formField->class('form-control '.config('crudoado.text_editor'));
        }

        if ($formFieldType === 'select') {
            $formField->setOptions($modelField->getOptions());
        }

        $formField->val($modelField->getValue());

        if (Request::old($modelField->getName())) {
            $formField->val(Request::old($modelField->getName()));
        }

        return $formField;
    }

    public function getValidationRules()
    {
        $rules = array();

        foreach ($this->fields as $field) {
            $rules[$field->getName()] = $field->getValidationRules();
        }

        return $rules;
    }
}
