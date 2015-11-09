<?php
namespace ANavallaSuiza\Crudoado\View\Composers;

use ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory as ModelAbstractorFactory;
use Request;

class SidebarComposer
{
    protected $modelFactory;

    public function __construct(ModelAbstractorFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    public function compose($view)
    {
        $models = config('crudoado.models');

        $url = Request::url();

        $items = [];

        foreach ($models as $modelName => $model) {
            $modelAbstractor = $this->modelFactory->getByName($modelName);

            $isActive = false;
            if (strpos($url, $modelAbstractor->getSlug()) !== false) {
                $isActive = true;
            }

            $items[] = [
                'route' => route('crudoado.model.index', $modelAbstractor->getSlug()),
                'name' => $modelAbstractor->getName(),
                'isActive' => $isActive
            ];
        }

        $view->with([
            'items' => $items
        ]);
    }
}
