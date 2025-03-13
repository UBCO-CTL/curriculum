<?php

return [

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app\\public'),
    ],

];
