<?php


namespace Anavel\Crud\Contracts\Controllers;

use Anavel\Crud\Contracts\Abstractor\Model;
use Illuminate\Http\Request;

interface CustomController
{
    public function setAbstractor(Model $modelAbstractor);

    public function index(Request $request, $model);

    public function create($model);

    public function store(Request $request, $model);

    public function show($model, $id);

    public function edit($model, $id);

    public function update(Request $request, $model, $id);

    public function destroy(Request $request, $model, $id);
}