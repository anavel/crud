<?php
namespace ANavallaSuiza\Crudoado\View\Composers;

class SidebarComposer
{

    public function compose($view)
    {
        $models = config('crudoado.models');

        $items = [];

        foreach ($models as $modelName => $model) {
            $items[] = [
                'route' => route('crudoado.model.index', $modelName),
                'name' => $modelName,
                'isActive' => false
            ];
        }

        $view->with([
            'items' => $items
        ]);
    }
}
