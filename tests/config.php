<?php

return [
    'Blog Posts' => 'App\Post',

    'Users' => [
        'model'                  => 'App\User',
        'icon'                   => 'fa-database',
        'soft_deletes'           => true,
        'fields_presentation'    => [
            'active'  => 'Is Active?',
            'role_id' => 'Role'
        ],
        'relations_presentation' => [
            'translations' => 'Translations',
            'posts'        => 'Published posts'
        ],
        'relations'              => [
            'translations' => [
                'type' => 'translation',
                'name' => 'translations'
            ],
            'group'        => [
                'type'    => 'select',
                'name'    => 'group',
                'display' => 'title'
            ],
            'posts'        => [
                'type'    => 'select-multiple',
                'name'    => 'posts',
                'display' => 'title'
            ],
            'photos'       => [
                'type'    => 'select-multiple',
                'name'    => 'photos',
                'display' => 'title'
            ],
            'roles'        => [
                'type'    => 'select-multiple-many-to-many',
                'name'    => 'roles',
                'display' => 'name'
            ]
        ],
        'list'                   => [
            'display' => ['id', 'username', 'fullname', 'active'],
            'hide'    => []
        ],
        'detail'                 => [
            'display' => ['id', 'username', 'password', 'fullname', 'info', 'active'],
            'hide'    => []
        ],
        'edit'                   => [
            'display'    => ['id', 'username', 'password', 'image'],
            'hide'       => [],
            'defaults'   => [
                'username' => 'Chompy',
                'info'     => [
                    'key'      => 'Value',
                    'otherKey' => 'Other value'
                ]
            ],
            'form_types' => [
                'username' => 'email',
                'password' => 'password',
                'image'    => 'file'
            ],
            'validation' => [
                'username' => 'required|email',
                'password' => 'required|min:8'
            ],
            'functions'  => [
                'fullname' => 'slugify',
                'password' => 'bcrypt'
            ]
        ]
    ]
];
