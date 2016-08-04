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
        $menuItems = $this->getSidebarItems();
        $view->with([
            'menuItems' => $menuItems,
            'headerName' => config('anavel-crud.name')
        ]);
    }

    /**
     * @return array
     */
    private function getSidebarItems()
    {
        $modelsGroups = config('anavel-crud.modelsGroups');
        $models = config('anavel-crud.models');
        $menuItems = [];

        if (!is_null($modelsGroups)) {
            foreach ($modelsGroups as $group => $items) {
                $menuItems[$group]['isActive'] = false;
                $menuItems[$group]['name'] = transcrud($group);
                $menuItems[$group]['items'] = [];
                foreach ($items as $itemName) {
                    try {
                        $item = $this->getModelItem($itemName);
                        $menuItems[$group]['items'][] = $item;
                        if (!$menuItems[$group]['isActive'] && $item['isActive']) {
                            $menuItems[$group]['isActive'] = true;
                        }
                        if (isset($models[$itemName])) {
                            unset($models[$itemName]);
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
                // Remove empty groups (resulting, most probably, of different permissions)
                if (count($menuItems[$group]['items']) < 1) {
                    unset($menuItems[$group]);
                }
            }
        }

        foreach ($models as $modelName => $model) {
            try {
                $item = $this->getModelItem($modelName);
            } catch (\Exception $e) {
                continue;
            }
            $menuItems[$modelName]['isActive'] =  $item['isActive'];
            $menuItems[$modelName]['name'] = $item['name'];
            $menuItems[$modelName]['items'][] = $item;
        }

        //Sort alphabetically de menu items
        usort($menuItems, function ($itemA, $itemB) {
            return strcmp($itemA['name'], $itemB['name']);
        });
        return $menuItems;
    }

    /**
     * @param $modelName
     * @return array
     * @throws \Exception
     */
    private function getModelItem($modelName)
    {
        $modelAbstractor = $this->modelFactory->getByName($modelName);
        if (array_key_exists('authorize', $config = $modelAbstractor->getConfig()) && $config['authorize'] === true) {
            if (Gate::denies('adminIndex', $modelAbstractor->getInstance())) {
                throw new \Exception('Access denied');
            }
        }

        $isActive = false;
        if (Route::current()->getParameter('model') === $modelAbstractor->getSlug()) {
            $isActive = true;
        }

        $item = [
            'route' => route('anavel-crud.model.index', $modelAbstractor->getSlug()),
            'name' => $modelAbstractor->getName(),
            'isActive' => $isActive
        ];

        return $item;
    }
}
