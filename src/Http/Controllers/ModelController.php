<?php
namespace Anavel\Crud\Http\Controllers;

use Anavel\Crud\Contracts\Abstractor\Model;
use Anavel\Foundation\Http\Controllers\Controller;
use Anavel\Crud\Contracts\Abstractor\ModelFactory as ModelAbstractorFactory;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use Anavel\Crud\Contracts\Controllers\CustomController;
use Anavel\Crud\Contracts\Form\Generator as FormGenerator;
use Anavel\Crud\Repository\Criteria\OrderByCriteria;
use Anavel\Crud\Repository\Criteria\SearchCriteria;
use Illuminate\Http\Request;
use App;

class ModelController extends Controller
{
    protected $modelFactory;
    protected $modelManager;
    protected $formGenerator;

    public function __construct(ModelAbstractorFactory $modelFactory, ModelManager $modelManager, FormGenerator $formGenerator)
    {
        $this->modelFactory = $modelFactory;
        $this->modelManager = $modelManager;
        $this->formGenerator = $formGenerator;
    }
    
    private function authorizeMethod(Model $modelAbstractor, $methodName)
    {
        if (array_key_exists('authorize', $config = $modelAbstractor->getConfig()) && $config['authorize'] === true) {
            $this->authorize($methodName, $modelAbstractor->getInstance());
        }
    }

    /**
     * @param Model $modelAbstractor
     * @return null|CustomController
     */
    private function customController(Model $modelAbstractor)
    {
        if (! $this instanceof CustomController) { //Avoid infinite recursion
            if (array_key_exists('controller', $config = $modelAbstractor->getConfig()) && (! empty($config['controller']))) {
                /** @var CustomController $controller */
                $controller = App::make($config['controller']);
                $controller->setAbstractor($modelAbstractor);
                return $controller;
            }
        }
        return null;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @param  string  $model
     * @return Response
     */
    public function index(Request $request, $model)
    {
        $modelAbstractor = $this->modelFactory->getBySlug($model);

        $this->authorizeMethod($modelAbstractor, 'adminIndex');

        if (! empty($customController = $this->customController($modelAbstractor))) {
            return $customController->index($request, $model);
        }

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());

        if ($request->has('search')) {
            $searchByColumns = array();

            foreach ($modelAbstractor->getListFields() as $field) {
                $searchByColumns[] = $field->getName();
            }

            $repository->pushCriteria(new SearchCriteria($searchByColumns, $request->get('search')));
        }

        if ($request->has('sort')) {
            $repository->pushCriteria(new OrderByCriteria($request->get('sort'), $request->get('direction') === 'desc' ? true : false));
        }

        $items = $repository->paginate(config('anavel-crud.list_max_results'));

        return view('anavel-crud::pages.index', [
            'abstractor' => $modelAbstractor,
            'items' => $items
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  string  $model
     * @return Response
     */
    public function create($model)
    {
        $modelAbstractor = $this->modelFactory->getBySlug($model);

        $this->authorizeMethod($modelAbstractor, 'adminCreate');

        if (! empty($customController = $this->customController($modelAbstractor))) {
            return $customController->create($model);
        }

        $form = $modelAbstractor->getForm(route('anavel-crud.model.store', $modelAbstractor->getSlug()));

        return view('anavel-crud::pages.create', [
            'abstractor' => $modelAbstractor,
            'form' => $form,
            'relations' => $modelAbstractor->getRelations()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @param  string  $model
     * @return Response
     */
    public function store(Request $request, $model)
    {
        $modelAbstractor = $this->modelFactory->getBySlug($model);

        $this->authorizeMethod($modelAbstractor, 'adminStore');

        if (! empty($customController = $this->customController($modelAbstractor))) {
            return $customController->store($request, $model);
        }

        // Sets the validation rules
        $modelAbstractor->getForm(route('anavel-crud.model.store', $modelAbstractor->getSlug()));

        $this->validate($request, $modelAbstractor->getValidationRules());

        $modelAbstractor->persist($request);

        session()->flash('anavel-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('anavel-crud::messages.alert_success_model_store_title'),
            'text'  => trans('anavel-crud::messages.alert_success_model_store_text')
        ]);

        return redirect()->route('anavel-crud.model.index', $model);
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $model
     * @param  int  $id
     * @return Response
     */
    public function show($model, $id)
    {
        $modelAbstractor = $this->modelFactory->getBySlug($model);

        $this->authorizeMethod($modelAbstractor, 'adminShow');

        if (! empty($customController = $this->customController($modelAbstractor))) {
            return $customController->show($model, $id);
        }

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        return view('anavel-crud::pages.show', [
            'abstractor' => $modelAbstractor,
            'item' => $item
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $model
     * @param  int  $id
     * @return Response
     */
    public function edit($model, $id)
    {
        /** @var Model $modelAbstractor */
        $modelAbstractor = $this->modelFactory->getBySlug($model, $id);

        $this->authorizeMethod($modelAbstractor, 'adminEdit');

        if (! empty($customController = $this->customController($modelAbstractor))) {
            return $customController->edit($model, $id);
        }

        $form = $modelAbstractor->getForm(route('anavel-crud.model.update', [$modelAbstractor->getSlug(), $id]));
        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        return view('anavel-crud::pages.edit', [
            'abstractor' => $modelAbstractor,
            'form' => $form,
            'item' => $item,
            'relations' => $modelAbstractor->getRelations()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  string  $model
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $model, $id)
    {
        $modelAbstractor = $this->modelFactory->getBySlug($model, $id);

        $this->authorizeMethod($modelAbstractor, 'adminUpdate');

        if (! empty($customController = $this->customController($modelAbstractor))) {
            return $customController->update($request, $model, $id);
        }

        // Sets the validation rules
        $modelAbstractor->getForm(route('anavel-crud.model.update', [$modelAbstractor->getSlug(), $id]));

        $this->validate($request, $modelAbstractor->getValidationRules());

        $modelAbstractor->persist($request);

        session()->flash('anavel-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('anavel-crud::messages.alert_success_model_update_title'),
            'text'  => trans('anavel-crud::messages.alert_success_model_update_text')
        ]);

        return redirect()->route('anavel-crud.model.edit', [$model, $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param  string  $model
     * @param  int  $id
     * @return Response
     */
    public function destroy(Request $request, $model, $id)
    {
        $modelAbstractor = $this->modelFactory->getBySlug($model);

        $this->authorizeMethod($modelAbstractor, 'adminDestroy');

        if (! empty($customController = $this->customController($modelAbstractor))) {
            return $customController->destroy($request, $model, $id);
        }

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        $item->delete();

        session()->flash('anavel-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('anavel-crud::messages.alert_success_model_destroy_title'),
            'text'  => trans('anavel-crud::messages.alert_success_model_destroy_text')
        ]);

        return redirect()->route('anavel-crud.model.index', $model);
    }
}
