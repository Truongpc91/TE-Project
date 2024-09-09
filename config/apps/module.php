<?php

return [
    'module' => [
        [
            'title' => 'QL Bài Viết',
            'icon' => 'fa fa-file',
            'name' => ['post'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Bài Viết',
                    'route' => "admin/post_catalogue/index"
                ],
                [
                    'title' => 'QL Bài Viết',
                    'route' => "admin/posts/index"
                ]
            ]
        ],
        [
            'title' => 'QL Thành Viên',
            'icon' => 'fa fa-th-large',
            'name' => ['users'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Thành Viên',
                    'route' => "admin/user_catalogue/index"
                ],
                [
                    'title' => 'QL Thành Viên',
                    'route' => "admin/users/index"
                ]
            ]
        ],
        [
            'title' => 'Cấu Hình Chung',
            'icon' => 'fa fa-file',
            'name' => ['language'],
            'subModule' => [
                [
                    'title' => 'QL Ngôn Ngữ',
                    'route' => "admin/language/index"
                ]
            ]
        ]
    ]
];
