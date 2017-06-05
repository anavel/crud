<?php

namespace Anavel\Crud\Abstractor\Eloquent\Relation;

use Anavel\Crud\Abstractor\Eloquent\Relation\Traits\CheckRelationCompatibility;
use Anavel\Crud\Contracts\Abstractor\Field;
use Illuminate\Http\Request;

class Translation extends Relation
{
    use CheckRelationCompatibility;

    protected $langs = [];

    protected $compatibleEloquentRelations = [
        'Illuminate\Database\Eloquent\Relations\HasMany',
    ];

    public function setup()
    {
        if (empty($this->langs)) {
            $this->langs = (array)config('anavel.translation_languages');
        }

        $this->checkRelationCompatibility();
    }

    /**
     * @return array
     */
    public function getEditFields($arrayKey = null)
    {
        /** @var \ANavallaSuiza\Laravel\Database\Contracts\Dbal\AbstractionLayer $dbal */
        $columns = $this->modelManager
            ->getAbstractionLayer(get_class($this->eloquentRelation->getRelated()))
            ->getTableColumns();

        $this->readConfig('edit');

        if (empty($columns) || empty($this->langs)) {
            return array();
        }

        if (empty($arrayKey)) {
            $arrayKey = $this->name;
        }

        $results = $this->eloquentRelation->getResults()->keyBy('locale');
        
        $translationFields = [];

        foreach ($this->langs as $key => $lang) {
            $tempFields = [];

            foreach ($columns as $columnName => $column) {
                if ($columnName === $this->eloquentRelation->getPlainForeignKey()) {
                    continue;
                }

                if ($columnName === $this->eloquentRelation->getParent()->getKeyName()) {
                    continue;
                }

                $formType = null;

                if ($columnName === 'locale') {
                    $formType = 'hidden';
                }

                $config = [
                    'name'         => $columnName,
                    'presentation' => ucfirst(transcrud($columnName)).' ['.$lang.']',
                    'form_type'    => $formType,
                    'no_validate'  => true,
                    'validation'   => null,
                    'functions'    => null,
                ];

                $config = $this->setConfig($config, $columnName);

                /** @var Field $field */
                $field = $this->fieldFactory
                    ->setColumn($column)
                    ->setConfig($config)
                    ->get();

                if ($columnName === 'locale') {
                    $field->setValue($lang);
                }

                if ($results->has($lang)) {
                    $item = $results->get($lang);

                    $field->setValue($item->getAttribute($columnName));
                }

                $tempFields[] = $field;
            }

            $translationFields[$arrayKey][$lang] = $tempFields;
        }

        return $translationFields;
    }

    /**
     * @param array|null $relationArray
     *
     * @return mixed
     */
    public function persist(array $relationArray = null, Request $request)
    {
        if (empty($relationArray)) {
            return;
        }

        $currentTranslations = $this->eloquentRelation->getResults()->keyBy('locale');

        foreach ($relationArray as $translation) {
            $isEmpty = true;

            foreach ($translation as $fieldKey => $fieldValue) {
                if ($isEmpty && $fieldKey != 'locale') {
                    $isEmpty = ($isEmpty === ($fieldValue === ''));
                }
            }

            if ($currentTranslations->has($translation['locale'])) {
                $translationModel = $currentTranslations->get($translation['locale']);
                
                if ($isEmpty) {
                    $translationModel->delete();
                    continue;
                }
            } else {
                $translationModel = $this->eloquentRelation->getRelated()->newInstance();
            }

            if ($isEmpty !== false) {
                continue;
            }
            
            $translationModel->setAttribute($this->eloquentRelation->getForeignKey(), $this->relatedModel->id);

            foreach ($translation as $fieldKey => $fieldValue) {
                $translationModel->setAttribute($fieldKey, $fieldValue);
            }

            $translationModel->save();
        }
    }

    /**
     * @return string
     */
    public function getDisplayType()
    {
        return self::DISPLAY_TYPE_INLINE;
    }
}
