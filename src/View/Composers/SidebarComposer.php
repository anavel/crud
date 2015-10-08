<?php
namespace ANavallaSuiza\Crudoado\View\Composers;

use Request;

class SidebarComposer
{

    public function compose($view)
    {
        $models = config('crudoado.models');

        $uri = Request::route()->uri();

        $items = [];

        foreach ($models as $modelName => $model) {
            $isActive = false;
            if (strpos($uri, $modelName) !== false) {
                $isActive = true;
            }

            $items[] = [
                'route' => route('crudoado.model.index', $modelName),
                'name' => $modelName,
                'isActive' => $isActive
            ];
        }

        $view->with([
            'items' => $items
        ]);
    }
}
