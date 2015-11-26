<?php
namespace ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation;

use ANavallaSuiza\Crudoado\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Field;
use App;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class MiniCrud extends Relation
{
    use CheckRelationCompatibility;

    protected $langs = [];

    protected $compatibleEloquentRelations = array(
        'Illuminate\Database\Eloquent\Relations\MorphMany'
    );

    public function setup()
    {
        $this->checkRelationCompatibility();
    }

    /**
     * @return array
     */
    public function getEditFields()
    {
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $dbal = $this->modelManager->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()));

        $fields = [];

        $columns = $dbal->getTableColumns();

        /** @var Collection $results */
        $results = $this->eloquentRelation->getResults();

        $results->put('emptyResult', '');
        if (! empty($columns)) {
            foreach ($results as $key => $result) {
                foreach ($columns as $columnName => $column) {
                    if ($columnName === $this->eloquentRelation->getPlainForeignKey()) {
                        continue;
                    }

                    if ($key !== 'emptyResult' && ($columnName === $this->eloquentRelation->getParent()->getKeyName())) {
                        continue;
                    }

                    $config = [
                        'name'         => $this->name . '[' . ($key === 'emptyResult' ? 0 : $result->id) . '][' . $columnName . ']',
                        'presentation' => $this->getPresentation(),
                        'form_type'    => null,
                        'no_validate'  => true,
                        'validation'   => null,
                        'functions'    => null
                    ];

                    /** @var Field $field */
                    $field = $this->fieldFactory
                        ->setColumn($column)
                        ->setConfig($config)
                        ->get();

                    if ($key !== 'emptyResult') {
                        $field->setValue($result->getAttribute($columnName));
                    }

                    $fields[] = $field;
                }

            }
        }


        return $fields;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function persist(Request $request)
    {
//        if (! empty($translationsArray = $request->input($this->name))) {
//            $currentTranslations = $this->eloquentRelation->get();
//            $currentTranslations = $currentTranslations->keyBy('locale');
//
//            foreach ($translationsArray as $translation) {
//                if ($currentTranslations->has($translation['locale'])) {
//                    $translationModel = $currentTranslations->get($translation['locale']);
//                } else {
//                    $translationModel = $this->eloquentRelation->getRelated()->newInstance();
//                }
//
//                $translationModel->setAttribute($this->eloquentRelation->getForeignKey(), $this->relatedModel->id);
//
//                foreach ($translation as $fieldKey => $fieldValue) {
//                    $translationModel->setAttribute($fieldKey, $fieldValue);
//                }
//
//                $translationModel->save();
//            }
//        }
    }
}
