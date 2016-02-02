<?php
namespace Anavel\Crud\Http\Form;

use Anavel\Crud\Contracts\Form\Generator as GeneratorContract;
use Anavel\Crud\Abstractor\Eloquent\Field;
use Doctrine\DBAL\Types\Type as DbalType;
use FormManager\FactoryInterface;
use Illuminate\Support\Collection;
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
        foreach ($this->fields as $fieldGroupName => $fieldGroup) {
            $fields = [];
            foreach ($fieldGroup as $field) {
                $fields[$field->getName()] = $field->getFormField();
            }
            $formFields[$fieldGroupName] = $this->factory->get('group', [$fields]);
        }
        $form->add($formFields);

        return $form;
    }

    public function getValidationRules()
    {
        $rules = array();

        foreach ($this->fields as $fieldGroupName => $fieldGroup) {
            foreach ($fieldGroup as $field) {
                $rules[$fieldGroupName][$field->getName()] = $field->getValidationRules();
            }
        }

        return $rules;
    }
}
