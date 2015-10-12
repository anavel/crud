<?php
namespace ANavallaSuiza\Crudoado\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractor;
use ANavallaSuiza\Laravel\Database\Contracts\Manager\ModelManager;
use ANavallaSuiza\Crudoado\Contracts\Form\Generator as FormGenerator;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    protected $abstractor;
    protected $manager;
    protected $generator;

    public function __construct(ModelAbstractor $abstractor, ModelManager $manager, FormGenerator $generator)
    {
        $this->abstractor = $abstractor;
        $this->manager = $manager;
        $this->generator = $generator;
    }

    /**
     * Display a listing of the resource.
     *
     * @param  string  $model
     * @return Response
     */
    public function index($model)
    {
        $this->abstractor->loadBySlug($model);

        $repository = $this->manager->getRepository($this->abstractor->getModel());
        $items = $repository->paginate(config('crudoado.list_max_results'));

        return view('crudoado::pages.index', [
            'abstractor' => $this->abstractor,
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
        $this->abstractor->loadBySlug($model);

        $this->generator->setModelFields($this->abstractor->getEditFields());
        $form = $this->generator->getForm(route('crudoado.model.store', $this->abstractor->getSlug()));

        return view('crudoado::pages.create', [
            'abstractor' => $this->abstractor,
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
        $this->abstractor->loadBySlug($model);

        $this->generator->setModelFields($this->abstractor->getEditFields());

        $this->validate($request, $this->generator->getValidationRules());

        $item = $this->manager->getModelInstance($this->abstractor->getModel());

        foreach ($this->abstractor->getEditFields() as $field) {
            $item->setAttribute($field->name(), $request->input($field->name()));
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
        $this->abstractor->loadBySlug($model);

        $repository = $this->manager->getRepository($this->abstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        return view('crudoado::pages.show', [
            'abstractor' => $this->abstractor,
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
        $this->abstractor->loadBySlug($model);

        $repository = $this->manager->getRepository($this->abstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        $this->generator->setModel($item);
        $this->generator->setModelFields($this->abstractor->getEditFields());
        $form = $this->generator->getForm(route('crudoado.model.update', [$this->abstractor->getSlug(), $id]));

        return view('crudoado::pages.edit', [
            'abstractor' => $this->abstractor,
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
        $this->abstractor->loadBySlug($model);

        $this->generator->setModelFields($this->abstractor->getEditFields());

        $this->validate($request, $this->generator->getValidationRules());

        $repository = $this->manager->getRepository($this->abstractor->getModel());
        $item = $repository->findByOrFail($repository->getModel()->getKeyName(), $id);

        foreach ($this->abstractor->getEditFields() as $field) {
            $item->setAttribute($field->name(), $request->input($field->name()));
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
        $this->abstractor->loadBySlug($model);

        $repository = $this->manager->getRepository($this->abstractor->getModel());
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
