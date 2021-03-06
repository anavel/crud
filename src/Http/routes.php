<?php

Route::group(
    [
        'prefix'    => 'crud',
        'namespace' => 'Anavel\Crud\Http\Controllers',
    ],
    function () {
        Route::get('/', [
            'as'   => 'anavel-crud.home',
            'uses' => 'HomeController@index',
        ]);

        // Model CRUD routes
        Route::get('{model}', [
            'as'   => 'anavel-crud.model.index',
            'uses' => 'ModelController@index',
        ]);

        Route::get('{model}/export/csv', [
            'as'   => 'anavel-crud.model.export.csv',
            'uses' => 'ModelController@exportCsv',
        ]);

        Route::get('{model}/create', [
            'as'   => 'anavel-crud.model.create',
            'uses' => 'ModelController@create',
        ]);

        Route::post('{model}', [
            'as'   => 'anavel-crud.model.store',
            'uses' => 'ModelController@store',
        ]);

        Route::get('{model}/{id}', [
            'as'   => 'anavel-crud.model.show',
            'uses' => 'ModelController@show',
        ]);

        Route::get('{model}/{id}/edit', [
            'as'   => 'anavel-crud.model.edit',
            'uses' => 'ModelController@edit',
        ]);

        Route::put('{model}/{id}', [
            'as'   => 'anavel-crud.model.update',
            'uses' => 'ModelController@update',
        ]);

        Route::delete('{model}/{id}', [
            'as'   => 'anavel-crud.model.destroy',
            'uses' => 'ModelController@destroy',
        ]);
    }
);
