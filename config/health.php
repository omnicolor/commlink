<?php

declare(strict_types=1);

return [
    /*
     * Configure what services are reported in the health check.
     *
     * Setting a service to false will set the health check to skip it. If
     * you're not running a service (such as Discord or IRC) and don't want
     * false health failures, turn the services off.
     */

    'discord' => env('HEALTH_CHECK_DISCORD', true),
    'irc' => env('HEALTH_CHECK_IRC', false),
    'redis' => env('HEALTH_CHECK_REDIS', true),
];
