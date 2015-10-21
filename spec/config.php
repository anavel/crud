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
];