<?php

return [
    'default' => 'local',

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'private',
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        'downloads' => [
            'driver' => 'local',
            'root' => storage_path('downloads'),
            'url' => env('APP_URL').'/download',
            'visibility' => 'public',
        ],
    ],
];
