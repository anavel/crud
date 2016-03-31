<?php
namespace Anavel\Crud\View\Composers;

use Anavel\Crud\Contracts\Abstractor\ModelFactory as ModelAbstractorFactory;
use Route;
use Gate;

class SidebarComposer
{
    protected $modelFactory;

    public function __construct(ModelAbstractorFactory $modelFactory)
    {
        $this->modelFactory = $modelFactory;
    }

    public function compose($view)
    {
        $models = config('anavel-crud.models');

        $items = [];

        foreach ($models as $modelName => $model) {
            $modelAbstractor = $this->modelFactory->getByName($modelName);

            if (array_key_exists('authorize', $config = $modelAbstractor->getConfig()) && $config['authorize'] === true) {
                if (Gate::denies('adminIndex', $modelAbstractor->getInstance())) {
                    continue;
                }
            }

            $isActive = false;
            if (Route::current()->getParameter('model') === $modelAbstractor->getSlug()) {
                $isActive = true;
            }

            $items[] = [
                'route' => route('anavel-crud.model.index', $modelAbstractor->getSlug()),
                'name' => $modelAbstractor->getName(),
                'isActive' => $isActive
            ];
        }

        $view->with([
            'items' => $items
        ]);
    }
}
