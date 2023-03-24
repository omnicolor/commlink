<?php

declare(strict_types=1);

return [
    /*
     * Third Party Services
     *
     * This file is for storing the credentials for third party services such as
     * Mailgun, Postmark, AWS and more. This file provides the de facto location
     * for this type of information, allowing packages to have a conventional
     * file to locate the various service credentials.
     */
    'discord' => [
        'avatar_default_extension' => env('DISCORD_EXTENSION_DEFAULT', 'png'),
        'allow_gif_avatars' => (bool)env('DISCORD_AVATAR_GIF', true),
        'client_id' => env('DISCORD_CLIENT_ID'),
        'client_secret' => env('DISCORD_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/discord/callback',
        'token' => env('DISCORD_TOKEN'),
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/google/callback',
    ],
    'slack' => [
        'client_id' => env('SLACK_CLIENT_ID'),
        'client_secret' => env('SLACK_CLIENT_SECRET'),
        'redirect' => env('APP_URL') . '/slack/callback',
    ],
];
