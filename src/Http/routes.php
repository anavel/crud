<?php

Route::group(
    [
        'prefix' => 'crudoado',
        'namespace' => 'ANavallaSuiza\Crudoado\Http\Controllers'
    ],
    function () {
        Route::get('/', [
            'as' => 'crudoado.home',
            'uses' => 'HomeController@index'
        ]);

        // Model CRUD routes
        Route::get('{model}', [
            'as' => 'crudoado.model.index',
            'uses' => 'ModelController@index'
        ]);

        Route::get('{model}/create', [
            'as' => 'crudoado.model.create',
            'uses' => 'ModelController@create'
        ]);

        Route::post('{model}', [
            'as' => 'crudoado.model.store',
            'uses' => 'ModelController@store'
        ]);

        Route::get('{model}/{id}', [
            'as' => 'crudoado.model.show',
            'uses' => 'ModelController@show'
        ]);

        Route::get('{model}/{id}/edit', [
            'as' => 'crudoado.model.edit',
            'uses' => 'ModelController@edit'
        ]);

        Route::put('{model}/{id}', [
            'as' => 'crudoado.model.update',
            'uses' => 'ModelController@update'
        ]);

        Route::delete('{model}/{id}', [
            'as' => 'crudoado.model.destroy',
            'uses' => 'ModelController@destroy'
        ]);
    }
);
