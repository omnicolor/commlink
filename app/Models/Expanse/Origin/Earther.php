<?php

declare(strict_types=1);

namespace App\Models\Expanse\Origin;

use App\Models\Expanse\Origin;

/**
 * Earther origin.
 */
class Earther extends Origin
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->description = 'With a population of some 30 billion, many '
            . 'Earthers are unemployed and live on government-provided Basic '
            . 'Assistance (generally known as just “Basic”) which provides for '
            . 'their essential food, housing, and medical needs, but little '
            . 'else. You are likely one of the few to leave Earth to find a '
            . 'new life elsewhere. As an Earther, your character has the '
            . 'following traits:||• Your native gravity is normal '
            . 'gravity—“Earth-normal” or 1 g. Earthers can and do learn to '
            . 'operate in lower gravity, but lack the instincts of people '
            . 'raised in it.||• Earthers have greater muscle and bone density '
            . 'from being raised in a gravity well, making them shorter and '
            . 'more broadly built than Belters or even native-born Martians, '
            . 'but Earthers in space need regular exercise and medical '
            . 'treatments to avoid muscle atrophy and bone density loss.';
        $this->name = 'Earther';
    }
}
