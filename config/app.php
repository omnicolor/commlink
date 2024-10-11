<?php

declare(strict_types=1);

use Illuminate\Support\ServiceProvider;

return [

    'providers' => ServiceProvider::defaultProviders()->merge([
        /*
         * Laravel Framework Service Providers...
         */

        /*
         * Package Service Providers...
         */
        SocialiteProviders\Manager\ServiceProvider::class,

        /*
         * Application Service Providers...
         */
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\BroadcastServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),

    'systems' => [
        'alien' => 'Alien',
        'avatar' => 'Avatar',
        'blistercritters' => 'Blister Critters',
        'capers' => 'Capers',
        'cyberpunkred' => 'Cyberpunk Red',
        'dnd5e' => 'Dungeons & Dragons 5th Edition',
        'expanse' => 'The Expanse',
        'legendofthefiverings4e' => 'Legend of the Five Rings 4th Edition',
        'shadowrunanarchy' => 'Shadowrun Anarchy',
        'shadowrun5e' => 'Shadowrun 5th Edition',
        'shadowrun6e' => 'Shadowrun 6th Edition',
        'startrekadventures' => 'Star Trek Adventures',
        'stillfleet' => 'Stillfleet',
        'subversion' => 'Subversion',
        'transformers' => 'Transformers',
    ],

];
