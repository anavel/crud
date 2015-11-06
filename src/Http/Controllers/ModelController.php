<?php
namespace ANavallaSuiza\Crudoado\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractor;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use ANavallaSuiza\Crudoado\Contracts\Form\Generator as FormGenerator;
use ANavallaSuiza\Crudoado\Repository\Criteria\OrderByCriteria;
use ANavallaSuiza\Crudoado\Repository\Criteria\SearchCriteria;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    protected $modelAbstractor;
    protected $modelManager;
    protected $formGenerator;

    public function __construct(ModelAbstractor $modelAbstractor, ModelManager $modelManager, FormGenerator $formGenerator)
    {
        $this->modelAbstractor = $modelAbstractor;
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
        $this->modelAbstractor->loadBySlug($model);

        $repository = $this->modelManager->getRepository($this->modelAbstractor->getModel());

        if ($request->has('search')) {
            $searchByColumns = array();

            foreach ($this->modelAbstractor->getListFields() as $field) {
                $searchByColumns[] = $field->name();
            }

            $repository->pushCriteria(new SearchCriteria($searchByColumns, $request->get('search')));
        }

        if ($request->has('sort')) {
            $repository->pushCriteria(new OrderByCriteria($request->get('sort'), $request->get('direction') === 'desc' ? true : false));
        }

        $items = $repository->paginate(config('crudoado.list_max_results'));

        return view('crudoado::pages.index', [
            'abstractor' => $this->modelAbstractor,
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
        $this->modelAbstractor->loadBySlug($model);

        $this->formGenerator->setModelFields($this->modelAbstractor->getEditFields());
        $form = $this->formGenerator->getForm(route('crudoado.model.store', $this->modelAbstractor->getSlug()));

        return view('crudoado::pages.create', [
            'abstractor' => $this->modelAbstractor,
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
        $this->modelAbstractor->loadBySlug($model);

        $this->formGenerator->setModelFields($this->modelAbstractor->getEditFields());

        $this->validate($request, $this->formGenerator->getValidationRules());

        $item = $this->modelManager->getModelInstance($this->modelAbstractor->getModel());

        foreach ($this->modelAbstractor->getEditFields() as $field) {
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
        $this->modelAbstractor->loadBySlug($model);

        $repository = $this->modelManager->getRepository($this->modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        return view('crudoado::pages.show', [
            'abstractor' => $this->modelAbstractor,
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
        $this->modelAbstractor->loadBySlug($model);

        $repository = $this->modelManager->getRepository($this->modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        $this->formGenerator->setModel($item);
        $this->formGenerator->setModelFields($this->modelAbstractor->getEditFields());
        $form = $this->formGenerator->getForm(route('crudoado.model.update', [$this->modelAbstractor->getSlug(), $id]));

        return view('crudoado::pages.edit', [
            'abstractor' => $this->modelAbstractor,
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
        $this->modelAbstractor->loadBySlug($model);

        $this->formGenerator->setModelFields($this->modelAbstractor->getEditFields());

        $this->validate($request, $this->formGenerator->getValidationRules());

        $repository = $this->modelManager->getRepository($this->modelAbstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        foreach ($this->modelAbstractor->getEditFields() as $field) {
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
        $this->modelAbstractor->loadBySlug($model);

        $repository = $this->modelManager->getRepository($this->modelAbstractor->getModel());
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
