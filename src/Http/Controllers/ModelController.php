<?php
namespace ANavallaSuiza\Crudoado\Http\Controllers;

use ANavallaSuiza\Adoadomin\Http\Controllers\Controller;

class ModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  string  $model
     * @return Response
     */
    public function index($model)
    {
        return view('crudoado::pages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  string  $model
     * @return Response
     */
    public function create($model)
    {
        return view('crudoado::pages.create');
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
