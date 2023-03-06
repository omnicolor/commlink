<?php

declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [
    /*
     * Default Log Channel
     *
     * This option defines the default log channel that gets used when writing
     * messages to the logs. The name specified in this option should match one
     * of the channels defined in the "channels" configuration array.
     */
    'default' => env('LOG_CHANNEL', 'stack'),
    'deprecations' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),

    /*
     * Log Channels
     * Here you may configure the log channels for your application. Out of the
     * box, Laravel uses the Monolog PHP logging library. This gives you a
     * variety of powerful log handlers / formatters to utilize.
     *
     * Available Drivers: "single", "daily", "slack", "syslog", "errorlog",
     * "monolog", "custom", "stack"
     */
    'channels' => [
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
        ],
        'deprecations' => [
            'driver' => 'single',
            'path' => storage_path('logs/deprecation-warnings.log'),
        ],
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'with_placeholders' => true,
        ],
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
        ],
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],
        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
        ],
    ],
];
