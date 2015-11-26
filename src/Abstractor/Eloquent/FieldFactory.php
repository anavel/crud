<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory as FieldAbstractorFactoryContract;
use ANavallaSuiza\Crudoado\Abstractor\Exceptions\FactoryException;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type as DbalType;
use FormManager\FactoryInterface as FormManagerFactory;

class FieldFactory implements FieldAbstractorFactoryContract
{
    /**
     * @var FormManagerFactory
     */
    protected $factory;

    /**
     * @var Column
     */
    protected $column;

    /**
     * @var array
     */
    protected $config;

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
        'select',
        'file'
    );

    public function __construct(FormManagerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     *
     */
    public function setColumn(Column $column)
    {
        $this->column = $column;

        return $this;
    }

    /**
     *
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     *
     */
    public function get()
    {
        $formElement = $this->getFormElement();

        $field = new Field($this->column, $formElement, $this->config['name'], $this->config['presentation']);

        if (! empty($this->config['validation'])) {
            $field->setValidationRules($this->config['validation']);
        }

        if (! empty($this->config['functions'])) {
            $field->setFunctions($this->config['functions']);
        }

        if (! empty($this->config['no_validate']) && $this->config['no_validate'] === true) {
            $field->noValidate(true);
        }

        if (get_class($formElement) === \FormManager\Fields\Password::class) {
            $field->hideValue(true);
            $field->saveIfEmpty(false);
            $field->noValidate(true);
        }

        return $field;
    }

    protected function getFormElement()
    {
        if (! empty($this->config['form_type'])) {
            if (! in_array($this->config['form_type'], $this->databaseTypeToFormType)) {
                throw new FactoryException("Unknown form type ".$this->config['form_type']);
            }

            $formElementType = $this->config['form_type'];
        } else {
            if (! array_key_exists($this->column->getType()->getName(), $this->databaseTypeToFormType)) {
                throw new FactoryException("No form type found for database type ".$this->column->getType()->getName());
            }

            $formElementType = $this->databaseTypeToFormType[$this->column->getType()->getName()];
        }

        if (! isset($this->config['defaults']) && is_array($this->config['defaults'])) {
            $formElementType = 'select';
        }


        $formElement = $this->factory->get($formElementType, []);

        if (! empty($this->config['attr']) && is_array($this->config['attr'])) {
            $formElement->attr($this->config['attr']);
        }

        if ($formElementType !== 'hidden') {
            $formElement->class('form-control')
                ->label($this->getPresentation())
                ->placeholder($this->getPresentation());
        }

        if ($formElementType === 'textarea') {
            $formElement->class('form-control '.config('crudoado.text_editor'));
        }

        if (! isset($this->config['defaults'])) {
            $formElement->val($this->config['defaults']);
        }

        return $formElement;
    }

    public function getPresentation()
    {
        return $this->config['presentation'] ? : ucfirst(str_replace('_', ' ', $this->config['name']));
    }
}
