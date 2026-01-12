<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. A "local" driver, as well as a variety of cloud
    | based drivers are available for your choosing. Just store away!
    |
    | Supported: "local", "ftp", "s3", "rackspace"
    |
    */

    'default' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => 'your-key',
            'secret' => 'your-secret',
            'region' => 'your-region',
            'bucket' => 'your-bucket',
        ],

        'admin' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'visibility' => 'public',
            'url' => 'http://localhost:8000/uploads/',
        ],

        'qiniu' => [
            'driver' => 'qiniu',
            'domains' => [
                'default' => env('QINIU_DOMAIN', ''),
                'https' => env('QINIU_HTTPS_DOMAIN', ''),
                'custom' => env('QINIU_CUSTOM_DOMAIN', ''),
            ],
            'access_key' => env('QINIU_ACCESS_KEY', ''),
            'secret_key' => env('QINIU_SECRET_KEY', ''),
            'bucket' => env('QINIU_BUCKET', 'laravel'),
            'notify_url' => env('QINIU_NOTIFY_URL', ''),
        ],

        'aliyun' => [
            'driver' => 'oss',
            'access_id' => env('ALIYUN_ACCESS_ID', ''),
            'access_key' => env('ALIYUN_ACCESS_KEY', ''),
            'bucket' => env('ALIYUN_BUCKET', 'laravel-admin'),
            'endpoint' => env('ALIYUN_ENDPOINT', 'oss-cn-shanghai.aliyuncs.com'),
        ],

    ],

];
