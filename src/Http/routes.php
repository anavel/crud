<?php

Route::group(
    [
        'prefix' => 'crudoado',
        'namespace' => 'ANavallaSuiza\Crudoado\Http\Controllers'
    ],
    function () {
        Route::get('/', 'HomeController@index');

        // Model CRUD routes
        Route::get('{model}', [
            'as' => 'model.index',
            'uses' => 'ModelController@index'
        ]);

        Route::get('{model}/create', [
            'as' => 'model.create',
            'uses' => 'ModelController@create'
        ]);

        Route::post('{model}', [
            'as' => 'model.store',
            'uses' => 'ModelController@store'
        ]);

        Route::get('{model}/{id}', [
            'as' => 'model.show',
            'uses' => 'ModelController@show'
        ]);

        Route::get('{model}/{id}/edit', [
            'as' => 'model.edit',
            'uses' => 'ModelController@edit'
        ]);

        Route::put('{model}/{id}', [
            'as' => 'model.update',
            'uses' => 'ModelController@update'
        ]);

        Route::delete('{model}/{id}', [
            'as' => 'model.destroy',
            'uses' => 'ModelController@destroy'
        ]);
    }
);
