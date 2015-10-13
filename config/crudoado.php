<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Displayed name
    |--------------------------------------------------------------------------
    |
    */
    'name' => 'CRUDoado',

    /*
    |--------------------------------------------------------------------------
    | Enabled actions
    |--------------------------------------------------------------------------
    |
    */
    'actions' => ['create', 'read', 'update', 'delete'],

    /*
    |--------------------------------------------------------------------------
    | Paginate lists by
    |--------------------------------------------------------------------------
    |
    */
    'list_max_results' => 15,

    /*
    |--------------------------------------------------------------------------
    | Formats
    |--------------------------------------------------------------------------
    |
    */
    'formats' => [
        'date'     => 'd/m/Y',
        'time'     => 'H:i',
        'datetime' => 'd/m/Y H:i:s',
        'number'   => '%.2f'
    ],

    /*
    |--------------------------------------------------------------------------
    | Text Editor
    |--------------------------------------------------------------------------
    | ckeditor, bootstrap-wysihtml5
    */
    'text_editor' => 'ckeditor',

    /*
    |--------------------------------------------------------------------------
    | Configured models
    |--------------------------------------------------------------------------
    |
    */
    'models' => [
        /*
        'Users' => 'App\User',

        // or

        'Users' => [
            'icon' => 'fa-database',
            'model' => 'App\User',
            'soft_deletes' => true,
            'fields_presentation' => [
                'active' => 'Is Active?',
                'role_id' => 'Role'
            ],
            'list' => [
                'fields' => ['id', 'username', 'fullname', 'active']
            ],
            'detail' => [
                'fields' => ['id', 'username', 'password', 'fullname', 'info', 'active']
            ],
            'edit' => [
                'fields' => ['id', 'username', 'password'],
                'inputs' => [
                    'username' => 'email',
                    'password' => 'password'
                ],
                'validation' => [
                    'username' => 'required|email',
                    'password' => 'required|min:8'
                ],
                'functions' => [
                    'fullname' => 'slugify',
                    'password' => 'encrypt'
                ]
            ]
        ]
        */
    ],

];
