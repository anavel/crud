<?php
namespace ANavallaSuiza\Crudoado\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\ModelFactory as ModelAbstractorFactory;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use ANavallaSuiza\Crudoado\Contracts\Form\Generator as FormGenerator;
use ANavallaSuiza\Crudoado\Repository\Criteria\OrderByCriteria;
use ANavallaSuiza\Crudoado\Repository\Criteria\SearchCriteria;
use Illuminate\Http\Request;

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

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());

        if ($request->has('search')) {
            $searchByColumns = array();

            foreach ($modelAbstractor->getListFields() as $field) {
                $searchByColumns[] = $field->name();
            }

            $repository->pushCriteria(new SearchCriteria($searchByColumns, $request->get('search')));
        }

        if ($request->has('sort')) {
            $repository->pushCriteria(new OrderByCriteria($request->get('sort'), $request->get('direction') === 'desc' ? true : false));
        }

        $items = $repository->paginate(config('crudoado.list_max_results'));

        return view('crudoado::pages.index', [
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

        $this->formGenerator->setModelFields($modelAbstractor->getEditFields());

        foreach ($modelAbstractor->getEditRelations() as $relation) {
            $this->formGenerator->addModelFields($relation->getEditFields());
        }
        $form = $this->formGenerator->getForm(route('crudoado.model.store', $modelAbstractor->getSlug()));

        return view('crudoado::pages.create', [
            'abstractor' => $modelAbstractor,
            'form' => $form
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

        $this->formGenerator->setModelFields($modelAbstractor->getEditFields());

        $this->validate($request, $this->formGenerator->getValidationRules());

        $item = $this->modelManager->getModelInstance($modelAbstractor->getModel());

        foreach ($modelAbstractor->getEditFields() as $field) {
            $item->setAttribute(
                $field->name(),
                $field->applyFunctions($request->input($field->name()))
            );
        }

        $item->save();

        session()->flash('adoadomin-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('crudoado::messages.alert_success_model_store_title'),
            'text'  => trans('crudoado::messages.alert_success_model_store_text')
        ]);

        return redirect()->route('crudoado.model.index', $model);
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

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        return view('crudoado::pages.show', [
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
        $modelAbstractor = $this->modelFactory->getBySlug($model);

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        $this->formGenerator->setModel($item);
        $this->formGenerator->setModelFields($modelAbstractor->getEditFields());
        $form = $this->formGenerator->getForm(route('crudoado.model.update', [$modelAbstractor->getSlug(), $id]));

        return view('crudoado::pages.edit', [
            'abstractor' => $modelAbstractor,
            'form' => $form
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
        $modelAbstractor = $this->modelFactory->getBySlug($model);

        $this->formGenerator->setModelFields($modelAbstractor->getEditFields());

        $this->validate($request, $this->formGenerator->getValidationRules());

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        foreach ($modelAbstractor->getEditFields() as $field) {
            $item->setAttribute(
                $field->name(),
                $field->applyFunctions($request->input($field->name()))
            );
        }

        $item->save();

        session()->flash('adoadomin-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('crudoado::messages.alert_success_model_update_title'),
            'text'  => trans('crudoado::messages.alert_success_model_update_text')
        ]);

        return redirect()->route('crudoado.model.index', $model);
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

        $repository = $this->modelManager->getRepository($modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        $item->delete();

        session()->flash('adoadomin-alert', [
            'type'  => 'success',
            'icon'  => 'fa-check',
            'title' => trans('crudoado::messages.alert_success_model_destroy_title'),
            'text'  => trans('crudoado::messages.alert_success_model_destroy_text')
        ]);

        return redirect()->route('crudoado.model.index', $model);
    }
}
