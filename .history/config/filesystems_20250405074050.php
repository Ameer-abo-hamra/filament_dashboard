<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been set up for each driver as an example of the required values.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => public_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],
        'item' => [
            'driver' => 'local',
            'root' => public_path('item'),
            'url' => 'https://wemarketglobal.com/cms/public/item',
            'visibility' => 'public',
            'throw' => false,
        ],
        'brand' => [
            'driver' => 'local',
            'root' => public_path('brand'),
            'url' => env("APP_URL" . "/brand"),
            'visibility' => 'public',
            'throw' => false,
        ],
        'category' => [
            'driver' => 'local',
            'root' => public_path('category'),
            'url' => env("APP_URL")."/cate",
            'visibility' => 'public',
            'throw' => false,
        ],
        //https://filament-dashboard-main-dvchw2.laravel.cloud/storage/brandPhoto/jXu2LNPrZ9Pe8FNGbXtzmXOul048xr-metaU2NyZWVuc2hvdCAyMDI0LTAxLTEyIDIyMzkzOC5wbmc=-.png
        //https://filament-dashboard-main-dvchw2.laravel.cloud/storage/brandPhoto/hfmXeZS538uMTSN8R0TXGu0XRgrPNF-metaU2NyZWVuc2hvdCAyMDI0LTAyLTI2IDIyNTMzMi5wbmc=-.png
        //http://127.0.0.1:8000/storage/brandPhoto/7Ru0WZinFF2Uvp7eSj4SZZJtj1w6zu-metaU2NyZWVuc2hvdCAyMDI0LTAyLTI2IDIyNTMzMi5wbmc=-.png
        //http://127.0.0.1:8000/storage/brandPhoto/7Ru0WZinFF2Uvp7eSj4SZZJtj1w6zu-metaU2NyZWVuc2hvdCAyMDI0LTAyLTI2IDIyNTMzMi5wbmc=-.png
        'page' => [
            'driver' => 'local',
            'root' => public_path('page'),
            'url' => 'https://wemarketglobal.com/cms/public/page',
            'visibility' => 'public',
            'throw' => false,
        ],
        'service' => [
            'driver' => 'local',
            'root' => public_path('service'),
            'url' => 'https://wemarketglobal.com/cms/public/service',
            'visibility' => 'public',
            'throw' => false,
        ],
        'slider' => [
            'driver' => 'local',
            'root' => public_path('slider'),
            'url' => 'https://wemarketglobal.com/cms/public/slider',
            'visibility' => 'public',
            'throw' => false,
        ],
        'contact' => [
            'driver' => 'local',
            'root' => public_path('contact'),
            'url' => 'https://wemarketglobal.com/cms/public/contact',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
