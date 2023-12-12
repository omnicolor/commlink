<?php

declare(strict_types=1);

use App\Models\Avatar\Status;

/**
 * Statuses from the Avatar RPG.
 */
return [
    /*
    '' => [
        'description-long' => '',
        'description-short' => '',
        'effect' => '',
        'id' => '',
        'name' => '',
        'page' => ,
        'ruleset' => 'core',
    ],
     */
    'doomed' => [
        'description-long' => 'Doomed is the status for when you’re drowning, or when you’re on fire, or when the stone around you isn’t simply immobilizing you—it’s crushing you. It represents a constant, ongoing pressure upon you, causing you to mark fatigue at a steady rate until you are free. The GM decides exactly how often you mark fatigue during play, unless you’re in exchanges—then, you should mark 1-fatigue at the beginning of each exchange. Remember that if you can’t mark more fatigue, you mark conditions instead.||Much of the time, you can’t be Doomed unless you’re first Impaired, or Impaired and Trapped. You won’t be drowning unless something holds you in the water; you won’t be slowly crushed by rock without first being trapped in rock. But sometimes it’s appropriate—your entire outfit, head to toe, can be set on fire by an angry firebending master without warning!',
        'description-short' => 'You’re in grave danger—mark 1-fatigue every few seconds (or each exchange) until you free yourself.',
        'effect' => Status::TYPE_NEGATIVE,
        'id' => 'doomed',
        'name' => 'Doomed',
        'page' => 151,
        'ruleset' => 'core',
    ],
    'empowered' => [
        'description-long' => 'Empowered is the status for when a Waterbender fights under a full moon, or a Firebender draws on the strength of Sozin’s Comet, or a swordswoman has been filled with the invigorating power of a friendly spirit—those moments when you are made far stronger than normal, almost always by something external to yourself.||You aren’t Empowered just because you have the high ground (which is closer to Favored) or because you set up some valuable defenses (which is closer to Prepared); you’re Empowered when you are strengthened far beyond your usual abilities. You usually can’t really pursue this status with a technique during combat—it isn’t the kind of status that a quick tweak to your environment can provide.||The GM adjudicates Empowered more than any other status, but they’re encouraged, as always, to be a fan of the PCs. If there’s an interesting, believable reason why someone is Empowered in this moment—for example, a Firebender standing in the middle of a raging forest fire—then the GM honors that reason by bestowing Empowered upon the character. Similarly, once that believable reason goes away—the forest fire is put out, or the Firebender moves away—then the status also goes away.||Empowered clears 1-fatigue at the end of each exchange, meaning after all approaches and techniques have been resolved. If you’re not in exchanges, then you clear 1-fatigue every few seconds, like an inversion of Doomed.',
        'description-short' => 'Your abilities are naturally stronger in this moment— clear 1-fatigue at the end of each exchange.',
        'effect' => Status::TYPE_POSITIVE,
        'id' => 'empowered',
        'name' => 'Empowered',
        'page' => 151,
        'ruleset' => 'core',
    ],
];
