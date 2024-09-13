<?php   
return [
    'module' => [
        [
            'title' => 'Article',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'Article Group',
                    'route' => 'admin/post_catalogue/index'
                ],
                [
                    'title' => 'Article',
                    'route' => 'admin/posts/index'
                ]
            ]
        ],
        [
            'title' => 'User Group',
            'icon' => 'fa fa-user',
            'name' => ['user'],
            'subModule' => [
                [
                    'title' => 'User Group',
                    'route' => 'admin/user_catalogue/index'
                ],
                [
                    'title' => 'User',
                    'route' => 'admin/users/index'
                ],
                [
                    'title' => 'Permission',
                    'route' => 'admin/permissions/index'
                ]
            ]
        ],
        [
            'title' => 'General',
            'icon' => 'fa fa-file',
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => 'Language',
                    'route' => 'admin/language/index'
                ],
            ]
        ]
    ],
];

