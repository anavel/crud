<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits\CheckRelationConfig;
use ANavallaSuiza\Crudoado\Repository\Criteria\InArrayCriteria;
use App;
use Illuminate\Http\Request;

class SelectMultiplePolymorphic extends SelectMultiple
{
    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\MorphMany'
    );


    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request)
    {
        if (! empty($selectArray = $request->input($this->name))) {
            /** @var \ANavallaSuiza\Laravel\Database\Contracts\Repository\Repository $repo */
            $repo = $this->modelManager->getRepository(get_class($this->eloquentRelation->getRelated()));

            $relationName = $this->name;
            $relatedKeyName = $this->eloquentRelation->getRelated()->getKeyName();
            $alreadyAssociated = $this->relatedModel->$relationName;

            $results = $repo->pushCriteria(
                new InArrayCriteria($relatedKeyName, $selectArray)
            )->all();

            $missing = $alreadyAssociated->diff($results);


            $keyName = $this->eloquentRelation->getPlainForeignKey();
            $morphKey = $this->eloquentRelation->getMorphType();

            foreach ($results as $result) {
                $result->$keyName = $this->relatedModel->getKey();
                $result->$morphKey = $this->eloquentRelation->getMorphClass();
                $result->save();
            }

            if (! $missing->isEmpty()) {
                foreach ($missing as $result) {
                    $result->$keyName = null;
                    $result->$morphKey = null;
                    $result->save();
                }
            }
        }
    }
}
