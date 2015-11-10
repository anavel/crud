<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Relation as RelationAbstractorContract;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Illuminate\Database\Eloquent\Model;

abstract class Relation implements RelationAbstractorContract
{
    const SELECT = 'select';
    const SELECT_MULTIPLE = 'select-multiple';
    const CHECKLIST = 'checklist';
    const MINI_CRUD = 'mini-crud';
    const TRANSLATION = 'translation';

    protected $eloquentTypeToRelationType = array(
        'Illuminate\Database\Eloquent\Relations\BelongTo'      => self::SELECT,
        'Illuminate\Database\Eloquent\Relations\BelongToMany'  => self::SELECT_MULTIPLE,
        'Illuminate\Database\Eloquent\Relations\HasMany'       => self::SELECT_MULTIPLE,
        'Illuminate\Database\Eloquent\Relations\HasManyTrough' => self::SELECT_MULTIPLE,
        'Illuminate\Database\Eloquent\Relations\HasOne'        => self::SELECT,
        'Illuminate\Database\Eloquent\Relations\HasOneOrMany'  => self::SELECT_MULTIPLE
    );

    protected $typesMap = array(
        self::SELECT          => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Select',
        self::SELECT_MULTIPLE => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\SelectMultiple',
        self::CHECKLIST       => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Checklist',
        self::MINI_CRUD       => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\MiniCrud',
        self::TRANSLATION     => 'ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Translation'
    );

    protected $name;
    protected $presentation;
    protected $type;
    /**
     * @var Model
     */
    protected $relatedModel;
    protected $modelManager;

    public function __construct(ModelManager $modelManager, Model $model, $name, $presentation)
    {
        $this->name = $name;
        $this->presentation = $presentation;
        $this->relatedModel = $model;

        $this->modelManager = $modelManager;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPresentation()
    {
        return $this->presentation;
    }

    public function getType()
    {
        return get_class($this);
    }

    /**
     * Factory method to create relation instances.
     *
     * @param string $name The name of the relation
     *
     * @return \ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Relation
     *
     * @throws \Exception
     */
    public static function getRelation($type)
    {
        if (! array_key_exists($type, self::$typesMap)) {
            throw new \Exception("Unexpected relation type ".$type);
        }

        return new self::$typesMap[$type]();
    }

    public static function getEloquentRelationEquivalence($eloquentRelation)
    {
        return self::$eloquentTypeToRelationType[$eloquentRelation];
    }
}
