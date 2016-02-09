<?php
namespace Anavel\Crud\Http\Form;

use Anavel\Crud\Contracts\Form\Generator as GeneratorContract;
use Anavel\Crud\Abstractor\Eloquent\Field;
use Doctrine\DBAL\Types\Type as DbalType;
use FormManager\FactoryInterface;
use Illuminate\Support\Collection;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
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
     * @var Collection
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
        $this->fields = array_merge_recursive($this->fields, $fields);
    }

    public function setModelRelations(Collection $relations)
    {
        $this->relations = $relations;
    }

    public function setRelatedModelFields(Collection $relations)
    {
        $this->setModelRelations($relations);

        if ($this->relations->count() > 0) {
            foreach ($this->relations as $relation) {
                if ($relation instanceof Collection) {
                    $this->addModelFields($relation->get('relation')->getEditFields());
                } else {
                    $this->addModelFields($relation->getEditFields());
                }
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
        foreach ($this->fields as $fieldGroupName => $fieldGroup) {
            $tempFields = $this->addFormFields($fieldGroup, $fieldGroupName);
            $formFields[key($tempFields)] = $this->factory->get('group', [$tempFields[key($tempFields)]]);
        }

        $form->add($formFields);


        return $form;
    }

    public function getValidationRules()
    {
        $rules = array();

        foreach ($this->fields as $fieldGroupName => $fieldGroup) {
            foreach ($fieldGroup as $field) {
                if (is_array($field)) {

                } else {
                    $rules[$fieldGroupName][$field->getName()] = $field->getValidationRules();
                }
            }
        }

        return $rules;
    }

    /**
     * @param array $fields
     * @param $key
     * @return array
     */
    protected function addFormFields(array $fields, $key)
    {
        $formFields = array();
        $tempFields = array();
            foreach ($fields as $fieldKey => $field) {
                if (is_array($field)) {
                    $group = $this->addFormFields($field, $fieldKey);
                    $tempFields[key($group)] = $this->factory->get('group', [$group[key($group)]]);
                } else {
                    $tempFields[$field->getName()] = $field->getFormField();
                }
            }
        $formFields[$key] = $tempFields;
        return $formFields;
    }
}