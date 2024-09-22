<?php   
return [
    'module' => [
        [
            'title' => 'Products',
            'icon' => 'fa fa-cube',
            'name' => ['product','attribute'],
            'subModule' => [
                [
                    'title' => 'Type Products',
                    'route' => 'admin/product_catalogue/index'
                ],
                [
                    'title' => ' Products',
                    'route' => 'admin/product/index'
                ],
                [
                    'title' => 'Type Attribute',
                    'route' => 'attribute/catalogue/index'
                ],
                [
                    'title' => 'Attributes',
                    'route' => 'admin/attribute/index'
                ],

            ]
        ],
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

