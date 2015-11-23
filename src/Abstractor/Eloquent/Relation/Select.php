<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Field;
use App;
use Illuminate\Http\Request;

class Select extends Relation
{
    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\BelongsTo'
    );

    public function checkEloquentRelationCompatibility()
    {
        if (! in_array(get_class($this->eloquentRelation), $this->compatibleEloquentRelations)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        return [];
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request)
    {
        // TODO: Implement persist() method.
    }
}
