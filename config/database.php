<?php

declare(strict_types=1);

use Illuminate\Support\Str;

return [

    'connections' => [
        'mysql-test' => [
            'driver' => 'mysql',
            'url' => env('DB_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge') . '-test',
            'username' => env('DB_USERNAME', 'forge') . '-test',
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => \extension_loaded('pdo_mysql') ? \array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

        'mongodb' => [
            'driver' => 'mongodb',
            'host' => env('MONGO_HOST', '127.0.0.1'),
            'port' => env('MONGO_PORT', 27017),
            'database' => env('MONGO_DATABASE', 'homestead'),
            'username' => env('MONGO_USERNAME', 'homestead'),
            'password' => env('MONGO_PASSWORD', 'secret'),
            'options' => [
                'database' => env('MONGO_AUTHENTICATION_DATABASE', 'admin'),
            ],
        ],

        'mongodb-test' => [
            'driver' => 'mongodb',
            'host' => env('MONGO_HOST', '127.0.0.1'),
            'port' => env('MONGO_PORT', 27017),
            'database' => env('MONGO_DATABASE', 'homestead'),
            'username' => env('MONGO_USERNAME', 'homestead'),
            'password' => env('MONGO_PASSWORD', 'secret'),
            'options' => [
                'database' => env('MONGO_AUTHENTICATION_DATABASE', 'admin'),
            ],
        ],
    ],

    'migrations' => [
        'table' => 'migrations',
        'update_date_on_publish' => false, // disable to preserve original behavior for existing applications
    ],

];
