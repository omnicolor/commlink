<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures\Species;

use App\Models\StarTrekAdventures\Species;
use App\Models\StarTrekAdventures\TalentArray;
use App\Models\StarTrekAdventures\Traits;

/**
 * Human species.
 */
class Human extends Species
{
    public function __construct()
    {
        $this->attributes = [];
        $this->description = 'Originating on the planet Earth in the Sol '
            . 'system, Humans are a resilient, diverse, and adaptable species, '
            . 'who developed from fractious, warring nations on the brink of '
            . 'mutual annihilation to a united, peaceful society in less than '
            . 'a century, and managed to forge alliances between former '
            . 'enemies within a century of achieving interstellar space '
            . 'flight. Earth is a founder and pivotal member of the United '
            . 'Federation of Planets, and many of the Federation’s '
            . 'institutions can be found on Earth. Humans often exhibit a '
            . 'dichotomy in their nature — being both driven to strong emotion '
            . 'and careful reason — and while they have largely grown beyond '
            . 'their warlike and divisive past, their drive and capacity for '
            . 'aggression are as much a part of their success as their '
            . 'curiosity and analytical minds.';
        $this->name = 'Human';
        $this->talents = new TalentArray();
        $this->trait = new Traits('human');
    }
}
