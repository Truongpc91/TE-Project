<?php   
return [
    'module' => [
        [
            'title' => 'QL sản phẩm',
            'icon' => 'fa fa-cube',
            'name' => ['product','attribute'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm sản phẩm',
                    'route' => 'product/catalogue/index'
                ],
                [
                    'title' => 'QL sản phẩm',
                    'route' => 'product/index'
                ],
                [
                    'title' => 'QL Loại thuộc tính',
                    'route' => 'attribute/catalogue/index'
                ],
                [
                    'title' => 'QL thuộc tính',
                    'route' => 'attribute/index'
                ],

            ]
        ],
        [
            'title' => 'QL Bài viết',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Bài Viết',
                    'route' => 'admin/post_catalogue/index'
                ],
                [
                    'title' => 'QL Bài Viết',
                    'route' => 'admin/posts/index'
                ]
            ]
        ],
        [
            'title' => 'QL Nhóm Thành Viên',
            'icon' => 'fa fa-user',
            'name' => ['user','permission'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Thành Viên',
                    'route' => 'admin/user_catalogue/index'
                ],
                [
                    'title' => 'QL Thành Viên',
                    'route' => 'admin/users/index'
                ],
                [
                    'title' => 'QL Quyền',
                    'route' => 'admin/permissions/index'
                ]
            ]
        ],
        [
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-file',
            'name' => ['language','generate'],
            'subModule' => [
                [
                    'title' => 'QL Ngôn ngữ',
                    'route' => 'admin/language/index'
                ],
                [
                    'title' => 'QL Module',
                    'route' => 'admin/generates/index'
                ],
                
            ]
        ]
    ],
];