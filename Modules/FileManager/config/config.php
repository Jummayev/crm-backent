<?php

return [
    'name' => 'FileManager',
    'max_file_size' => env('FILE_MAX_SIZE', 102_400), // 10MB
    'max_files_per_upload' => 10,
    'cdn_domain' => env('CDN_DOMAIN'),

    // Virus scanning (optional)
    'scan_for_viruses' => env('FILE_SCAN_VIRUSES', false),

    // Storage path (outside the public directory!)
    'storage_path' => base_path('/static'),

    'allowed_ext' => [
        // Images
        'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg',

        // Documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',

        // Archives
        'zip', 'rar',
    ],

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

    // BLOCKED extensions
    'blocked_extensions' => [
        // PHP executables
        'php', 'php3', 'php4', 'php5', 'php7', 'php8',
        'phtml', 'phar', 'phps', 'pht', 'phpt', 'pgif',
        'inc', 'shtml', 'phtm',

        // Server scripts
        'cgi', 'pl', 'py', 'sh', 'bash', 'ksh', 'zsh',
        'asp', 'aspx', 'cer', 'asa', 'jsp', 'jspx',
        'rb', 'rbw',

        // Executables
        'exe', 'com', 'bat', 'cmd', 'msi', 'scr', 'dll',
        'vbs', 'vbe', 'js', 'jse', 'wsf', 'wsh',
        'bin', 'run', 'app', 'deb', 'rpm', 'dmg',

        // Config files
        'htaccess', 'htpasswd', 'ini', 'conf', 'config', 'cnf',
        'env', 'environment',

        // Security files
        'pem', 'key', 'ppk', 'der', 'csr', 'pfx', 'p12',

        // Database
        'sql', 'sqlite', 'db', 'dbf', 'mdb',

        // Source code
        'c', 'cpp', 'h', 'java', 'go',

        // Special
        'shtm', 'stm', 'fcgi',
    ],
];
