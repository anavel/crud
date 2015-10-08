<?php
namespace ANavallaSuiza\Crudoado\View\Composers;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractor;
use Request;

class SidebarComposer
{
    protected $abstractor;

    public function __construct(ModelAbstractor $abstractor)
    {
        $this->abstractor = $abstractor;
    }

    public function compose($view)
    {
        $models = config('crudoado.models');

        $url = Request::url();

        $items = [];

        foreach ($models as $modelName => $model) {
            $this->abstractor->loadByName($modelName);

            $isActive = false;
            if (strpos($url, $this->abstractor->getSlug()) !== false) {
                $isActive = true;
            }

            $items[] = [
                'route' => route('crudoado.model.index', $this->abstractor->getSlug()),
                'name' => $this->abstractor->getName(),
                'isActive' => $isActive
            ];
        }

        $view->with([
            'items' => $items
        ]);
    }
}
