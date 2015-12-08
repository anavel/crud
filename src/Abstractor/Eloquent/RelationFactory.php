<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\RelationFactory as RelationAbstractorFactoryContract;
use ANavallaSuiza\Crudoado\Abstractor\Exceptions\FactoryException;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\FieldFactory;

class RelationFactory implements RelationAbstractorFactoryContract
{
    const SELECT = 'select';
    const SELECT_MULTIPLE = 'select-multiple';
    const SELECT_MULTIPLE_POLYMORPHIC = 'select-multiple-polymorphic';
    const CHECKLIST = 'checklist';
    const MINI_CRUD = 'mini-crud';
    const MINI_CRUD_SINGLE = 'mini-crud-single';
    const MINI_CRUD_POLYMORPHIC = 'mini-crud-polymorphic';
    const TRANSLATION = 'translation';

    protected $eloquentTypeToRelationType = array(
        'Illuminate\Database\Eloquent\Relations\BelongsTo'     => self::SELECT,
        'Illuminate\Database\Eloquent\Relations\BelongsToMany' => self::SELECT_MULTIPLE,
        'Illuminate\Database\Eloquent\Relations\HasMany'       => self::SELECT_MULTIPLE,
        'Illuminate\Database\Eloquent\Relations\HasManyTrough' => self::SELECT_MULTIPLE,
        'Illuminate\Database\Eloquent\Relations\HasOne'        => self::SELECT,
        'Illuminate\Database\Eloquent\Relations\HasOneOrMany'  => self::SELECT_MULTIPLE,
        'Illuminate\Database\Eloquent\Relations\MorphMany'     => self::MINI_CRUD_POLYMORPHIC,
        'Illuminate\Database\Eloquent\Relations\MorphOne'      => self::MINI_CRUD_SINGLE
    );

    protected $typesMap = array(
        self::SELECT                      => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Select',
        self::SELECT_MULTIPLE             => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\SelectMultiple',
        self::SELECT_MULTIPLE_POLYMORPHIC => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\SelectMultiplePolymorphic',
        self::CHECKLIST                   => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Checklist',
        self::MINI_CRUD                   => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\MiniCrud',
        self::MINI_CRUD_SINGLE            => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\MiniCrudSingle',
        self::MINI_CRUD_POLYMORPHIC       => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\MiniCrudPolymorphic',
        self::TRANSLATION                 => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Translation'
    );

    protected $modelManager;
    protected $fieldFactory;

    /**
     * @var EloquentModel
     */
    protected $model;
    protected $config;

    public function __construct(ModelManager $modelManager, FieldFactory $fieldFactory)
    {
        $this->modelManager = $modelManager;
        $this->fieldFactory = $fieldFactory;
        $this->config = array();
    }

    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;

        return $this;
    }

    public function get($name)
    {
        if (! method_exists($this->model, $name)) {
            throw new FactoryException("Relation " . $name . " does not exist on " . get_class($this->model));
        }

        $relationInstance = $this->model->$name();
        $relationEloquentType = get_class($relationInstance);


        if (empty($this->config['type'])) {
            if (! array_key_exists($relationEloquentType, $this->eloquentTypeToRelationType)) {
                throw new FactoryException($relationEloquentType . " relation not supported");
            }

            $type = $this->eloquentTypeToRelationType[$relationEloquentType];
        } else {
            $type = $this->config['type'];
        }

        if (! array_key_exists($type, $this->typesMap)) {
            throw new FactoryException("Unexpected relation type: " . $type);
        }

        $this->config['name'] = $name;

        return new $this->typesMap[$type](
            $this->config,
            $this->modelManager,
            $this->model,
            $relationInstance,
            $this->fieldFactory
        );
    }
}
