<?php

return [
    'name' => 'CRUDoado',

    'actions' => ['create', 'read', 'update', 'delete'],

    'list_max_results' => 15,

    'formats' => [
        'date'     => 'd/m/Y',
        'time'     => 'H:i',
        'datetime' => 'd/m/Y H:i:s',
        'number'   => '%.2f'
    ],

    'models' => [
        /*
        'Users' => 'App\User',

        // or

        'Users' => [
            'model' => 'App\User',
            'soft_deletes' => true,
            'list' => [
                'fields' => ['id', 'username', 'email', 'Activo?' => 'active']
            ],
            'detail' => [
                'fields' => ['id', 'username', 'password']
            ]
        ]
        */
    ],

];
