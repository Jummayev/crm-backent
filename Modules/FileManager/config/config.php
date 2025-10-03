<?php

return [
    'name' => 'FileManager',
    'allowed_ext' => ['jpeg', 'jpg', 'svg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'pdf', 'zip', 'rar'],
    'thumbs' => [
        'icon' => [
            'w' => 50,
            'h' => 50,
            'q' => 80,
            'slug' => 'icon',
        ],
        'small' => [
            'w' => 320,
            'h' => 240,
            'q' => 70,
            'slug' => 'small',
        ],
        'low' => [
            'w' => 640,
            'h' => 480,
            'q' => 70,
            'slug' => 'low',
        ],
        'normal' => [
            'w' => 1024,
            'h' => 728,
            'q' => 70,
            'slug' => 'normal',
        ],
    ],
    'images_ext' => [
        'jpg',
        'jpeg',
        'png',
        'bmp',
        'gif',
    ],
    'cdn_domain' => env('CDN_DOMAIN'),
];
