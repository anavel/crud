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
    | Displayed icon
    |--------------------------------------------------------------------------
    |
    */
    'icon' => 'fa-database',

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
            'model' => 'App\User',
            'icon' => 'fa-database',
            'soft_deletes' => true,
            'fields_presentation' => [
                'active' => 'Is Active?',
                'role_id' => 'Role'
            ],
            'list' => [
                'displayed' => ['id', 'username', 'fullname', 'active'],
                'hidden'    => []
            ],
            'detail' => [
                'displayed' => ['id', 'username', 'password', 'fullname', 'info', 'active'],
                'hidden'    => []
            ],
            'edit' => [
                'displayed' => ['id', 'username', 'password'],
                'hidden'    => [],
                'form_types' => [
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
