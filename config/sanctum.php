<?php

declare(strict_types=1);

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;

return [
    /*
     * Stateful Domains
     *
     * Requests from the following domains / hosts will receive stateful API
     * authentication cookies. Typically, these should include your local and
     * production domains which access your API via a frontend SPA.
     */
    'stateful' => explode(',', (string)env(
        'SANCTUM_STATEFUL_DOMAINS',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1,commlink.digitaldarkness.com'
    )),

    /*
     * Expiration Minutes
     *
     * This value controls the number of minutes until an issued token will be
     * considered expired. If this value is null, personal access tokens do not
     * expire. This won't tweak the lifetime of first-party sessions.
     */
    'expiration' => null,

    /*
     * Sanctum Middleware
     *
     * When authenticating your first-party SPA with Sanctum you may need to
     * customize some of the middleware Sanctum uses while processing the
     * request. You may change the middleware listed below as required.
     */
    'middleware' => [
        'authenticate_session' => AuthenticateSession::class,
        'encrypt_cookies' => EncryptCookies::class,
        'validate_csrf_token' => ValidateCsrfToken::class,
    ],
];
