<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Displayed name
    |--------------------------------------------------------------------------
    |
    */
    'name' => 'CRUD',

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
        'number'   => '%.2f',
    ],

    /*
    |--------------------------------------------------------------------------
    | File uploads path
    |--------------------------------------------------------------------------
    |
    */
    'uploads_path' => '/public/uploads/',

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
            'relations' => [
                // Types: select, select-multiple, check, mini-crud
                'posts' => 'select-multiple',
                'translations'
            ],
            'list' => [
                'display' => ['id', 'username', 'fullname', 'active'],
                'hide'    => []
            ],
            'detail' => [
                'display' => ['id', 'username', 'password', 'fullname', 'info', 'active'],
                'hide'    => []
            ],
            'edit' => [
                'display' => ['id', 'username', 'password'],
                'hide'    => [],
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
                    'password' => 'bcrypt'
                ]
            ]
        ]

        // And much more
          'Blog Posts'      => [
            'model'      => '\App\Database\Models\Post',
            'controller' => '\App\Http\Controllers\Admin\BlogPostController',
            'authorize'  => false,
            'list'       => [
                'display' => ['id', 'slug', 'title', 'publication_date'],
            ],
            'edit'       => [
                'form_types' => [
                    'image' => 'file',
                ]
            ],
            'relations'  => [
                'tags'   => [
                    'name'    => 'tags',
                    'display' => 'name'
                ],
                'author' => [
                    'name'    => 'author',
                    'display' => 'name'
                ],
            ]
        ],
        'Blog Tags'       => [
            'model'     => '\App\Database\Models\PostTag',
            'authorize' => false,
        ]
        */
    ],
    'modelsGroups' => [/*'Blog' => ['Blog Posts', 'Blog Tags']*/],

];
