<?php
namespace ANavallaSuiza\Crudoado\View\Composers;

use Request;
use EasySlugger\Slugger;

class SidebarComposer
{

    public function compose($view)
    {
        $models = config('crudoado.models');

        $url = Request::url();

        $items = [];

        foreach ($models as $modelName => $model) {
            $modelSlug = Slugger::slugify($modelName);

            $isActive = false;
            if (strpos($url, $modelSlug) !== false) {
                $isActive = true;
            }

            $items[] = [
                'route' => route('crudoado.model.index', $modelSlug),
                'name' => $modelName,
                'isActive' => $isActive
            ];
        }

        $view->with([
            'items' => $items
        ]);
    }
}
