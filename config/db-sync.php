<?php

return [
    'default_source' => env('DB_SYNC_SOURCE', 'sandbox'),

    'dump_binary' => env('DB_SYNC_MYSQLDUMP_BIN', 'mysqldump'),
    'client_binary' => env('DB_SYNC_MYSQL_BIN', 'mysql'),

    'sources' => [
        'sandbox' => [
            'host' => env('DB_SANDBOX_HOST'),
            'port' => env('DB_SANDBOX_PORT', 3306),
            'database' => env('DB_SANDBOX_DATABASE'),
            'username' => env('DB_SANDBOX_USERNAME'),
            'password' => env('DB_SANDBOX_PASSWORD'),
        ],
        'production' => [
            'host' => env('DB_PRODUCTION_HOST'),
            'port' => env('DB_PRODUCTION_PORT', 3306),
            'database' => env('DB_PRODUCTION_DATABASE'),
            'username' => env('DB_PRODUCTION_USERNAME'),
            'password' => env('DB_PRODUCTION_PASSWORD'),
        ],
    ],

    'target_connection' => env('DB_SYNC_TARGET_CONNECTION', env('DB_CONNECTION', 'mysql')),
];
