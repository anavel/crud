<?php

return [
    'Blog Posts' => 'App\Post',

    'Users' => [
            'model' => 'App\User',
            'icon' => 'fa-database',
            'soft_deletes' => true,
            'fields_presentation' => [
                'active' => 'Is Active?',
                'role_id' => 'Role'
            ],
            'relations_presentation' => [
                'translations' => 'Translations',
                'posts' => 'Published posts'
            ],
            'relations' => [
                'translations' => [
                    'type' => 'translation',
                    'name' => 'translations'
                ],
                'roles' => 'select-multiple',
                'posts'
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
];
