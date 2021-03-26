<?php

declare(strict_types=1);

namespace App\Models\Expanse\Origin;

use App\Models\Expanse\Origin;

/**
 * Belter origin.
 */
class Belter extends Origin
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->description = 'You were born and raised in the Black, on a '
            . 'station or ship, and have lived most, if not all, of your life '
            . 'out in the Belt or beyond. Separated from death by nothing more '
            . 'than basic support systems your whole life, you have learned to '
            . 'be cautious and aware of your environment. As a Belter, your '
            . 'character has the following traits:||• Your native gravity is '
            . 'microgravity. Belters are most comfortable “on the float” and '
            . 'handle moving in free-fall easily. You automatically have the '
            . 'Dexterity (Free-fall) focus. Conversely, Earth-normal gravity '
            . 'is crushingly heavy for a Belter.||• You speak Belter Creole, a '
            . 'combination of loan-words and phrases from various languages, '
            . 'combined with gestures useful for communicating while wearing a '
            . 'vac suit and unable to speak.||• Belters tend to be tall and '
            . 'willowy as a result of being raised in low- or microgravity '
            . 'environments. Regimens of bone-density drugs and genetic '
            . 'treatments are needed to keep Belters healthy, and some Belters '
            . 'have minor physical abnormalities because of this.||• Belters '
            . 'often have a diverse ethnic heritage, given the “melting pot” '
            . 'of the Belt, with ancestors from many different Earth cultures.';
        $this->name = 'Belter';
    }
}
