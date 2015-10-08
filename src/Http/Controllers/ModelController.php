<?php
namespace ANavallaSuiza\Crudoado\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;
use ANavallaSuiza\Crudoado\Contracts\Abstractor\Model as ModelAbstractor;

class ModelController extends Controller
{
    protected $abstractor;

    public function __construct(ModelAbstractor $abstractor)
    {
        $this->abstractor = $abstractor;
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

        return view('crudoado::pages.index', [
            'abstractor' => $this->abstractor,
            'items' => collect()
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

        return view('crudoado::pages.create', [
            'abstractor' => $this->abstractor
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  string  $model
     * @return Response
     */
    public function store($model)
    {
        //
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
        return view('crudoado::pages.show');
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
        return view('crudoado::pages.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  string  $model
     * @param  int  $id
     * @return Response
     */
    public function update($model, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $model
     * @param  int  $id
     * @return Response
     */
    public function destroy($model, $id)
    {
        //
    }
}
