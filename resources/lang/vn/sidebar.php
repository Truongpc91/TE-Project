<?php   
return [
    'module' => [
        [
            'title' => 'Dashboard',
            'icon' => 'fa fa-database',
            'name' => ['dashboard'],
            'class' => 'special'
        ],
        [
            'title' => 'QL sản phẩm',
            'icon' => 'fa fa-cube',
            'name' => ['product','attribute'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm sản phẩm',
                    'route' => 'admin/product_catalogue/index'
                ],
                [
                    'title' => 'QL sản phẩm',
                    'route' => 'admin/product/index'
                ],
                [
                    'title' => 'QL Loại thuộc tính',
                    'route' => 'admin/attribute_catalogue/index'
                ],
                [
                    'title' => 'QL thuộc tính',
                    'route' => 'admin/attribute/index'
                ],

            ]
        ],
        [
            'title' => 'QL Đơn hàng',
            'icon' => 'fa fa-shopping-bag',
            'name' => ['order'],
            'subModule' => [
                [
                    'title' => 'QL Đơn hàng',
                    'route' => 'admin/order/index'
                ],
            ]
        ],
        [
            'title' => 'QL Nhóm Khách Hàng',
            'icon' => 'fa fa-user',
            'name' => ['customer','customer_catalogue'],
            'subModule' => [
                [
                    'title' => 'QL Nhóm Khách Hàng',
                    'route' => 'admin/customer_catalogue/index'
                ],
                [
                    'title' => 'QL Khách Hàng',
                    'route' => 'admin/customer/index'
                ],
            ]
        ],
        [
            'title' => 'QL Marketing',
            'icon' => 'fa fa-money',
            'name' => ['promotion'],
            'subModule' => [
                [
                    'title' => 'QL Marketing',
                    'route' => 'admin/promotion/index'
                ], 
                [
                    'title' => 'QL Mã giảm giá',
                    'route' => 'admin/coupon/index'
                ], 
                [
                    'title' => 'QL Nguồn khách hàng',
                    'route' => 'admin/source/index'
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
            'title' => 'QL Banner & Slide',
            'icon' => 'fa fa-picture-o',
            'name' => ['slide'],
            'subModule' => [
                [
                    'title' => 'Quản lý Slide',
                    'route' => 'admin/slide/index'
                ],
            ]
        ],
        [
            'title' => 'QL Menu',
            'icon' => 'fa fa-bars',
            'name' => ['menu'],
            'subModule' => [
                [
                    'title' => 'Cài đặt Menu',
                    'route' => 'admin/menu/index'
                ],
            ]
        ],
        [
            'title' => 'Cấu hình chung',
            'icon' => 'fa fa-file',
            'name' => ['language','generate', 'system'],
            'subModule' => [
                [
                    'title' => 'QL Ngôn ngữ',
                    'route' => 'admin/language/index'
                ],
                [
                    'title' => 'QL Module',
                    'route' => 'admin/generates/index'
                ],
                [
                    'title' => 'Cấu hình hệ thống',
                    'route' => 'admin/system/index'
                ],
                [
                    'title' => 'Quản lý Widget',
                    'route' => 'admin/widget/index'
                ],
            ]
        ]
    ],
];
