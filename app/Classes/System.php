<?php

namespace App\Classes;

class System
{

    public function config()
    {
        $data['homepage'] = [
            'label' => 'Thông tin chung',
            'description' => 'Cài đặt đầy đủ thông tin chung của Website. Tên thương hiệu website, Logo, Favicon, vv...',
            'value' => [
                'company'       => ['type' => 'text', 'label' => 'Tên công ty'],
                'brand'         => ['type' => 'text', 'label' => 'Tên thương hiệu'],
                'slogan'        => ['type' => 'text', 'label' => 'Tên thương hiệu'],
                'logo'          => ['type' => 'file', 'label' => 'Logo website', 'tittle' => 'Click vào ô phía dưới để upload ảnh Logo'],
                'favicon'       => ['type' => 'file', 'label' => 'Favicon', 'tittle' => 'Click vào ô phía dưới để upload ảnh Logo'],
                'copyright'     => ['type' => 'text', 'label' => 'Copyright'],
                'website' => [
                    'type' => 'select',
                    'label' => 'Tình trạng Website',
                    'options' => [
                        'open' => 'Mở cửa',
                        'close' => 'Website đang bảo trì'
                    ]
                ],
                'short_intro' => ['type' => 'editor', 'label' => 'Giới thiệu ngắn']
            ],

        ];

        $data['contact'] = [
            'label' => 'Thông liên hệ',
            'description' => 'Cài đặt thông tin liên hệ của Website ví dụ: Địa chỉ công ty, Hotline,Bản đồ, vv...',
            'value' => [
                'office'       => ['type' => 'text', 'label' => 'Địa chỉ công ty'],
                'address'       => ['type' => 'text', 'label' => 'Văn phòng giao dịch'],
                'hotline'       => ['type' => 'text', 'label' => 'Hotline'],
                'technical_phone'       => ['type' => 'text', 'label' => 'Hotline kỹ thuật'],
                'sell_phone'       => ['type' => 'text', 'label' => 'Hotline kinh doanh'],
                'phone'       => ['type' => 'text', 'label' => 'Số cố định'],
                'email'       => ['type' => 'text', 'label' => 'Email'],
                'tax'       => ['type' => 'text', 'label' => 'Mã số thuế'],
                'website'       => ['type' => 'text', 'label' => 'Website'],
                'map'       => [
                    'type' => 'textarea',
                    'label' => 'Bản đồ',
                    'link' => [
                        'text' => 'Hướng dẫn thiết lập bản đồ',
                        'target' => '_blank',
                        'href' => 'https://www.thegioididong.com/game-app/cach-them-tao-dia-diem-tren-google-maps-de-dang-va-nhanh-1277606',
                    ]
                ],
            ]
        ];

        $data['seo'] = [
            'label' => 'Cấu hình SEO cho trang chủ Website',
            'description' => 'Cài đặt đầy đủ thông tin vè SEO của trang chủ website. Bao gồm tiêu đề SEO, từ khóa SEO, Meta image, vv...',
            'value' => [
                'meta_title'          => ['type' => 'text', 'label' => 'Tiêu đề SEO'],
                'meta_keyword'        => ['type' => 'text', 'label' => 'Từ khóa SEO'],
                'meta_description'    => ['type' => 'text', 'label' => 'Mô tả SEO'],
                'meta_image'          => ['type' => 'file', 'label' => 'Ảnh SEO'],
            ],

        ];
        return $data;
    }
}
