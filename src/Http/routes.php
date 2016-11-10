<?php

use \Illuminate\Support\Facades\Route;

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

        // Batch actions
        /*Route::put('{model}/batch', [
            'as'   => 'anavel-crud.model.batch-update',
            'uses' => 'ModelController@batchUpdate'
        ]);

        Route::delete('{model}/batch', [
            'as'   => 'anavel-crud.model.batch-delete',
            'uses' => 'ModelController@batchDestroy'
        ]);*/
    }
);
