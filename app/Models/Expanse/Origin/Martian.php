<?php

declare(strict_types=1);

namespace App\Models\Expanse\Origin;

use App\Models\Expanse\Origin;

/**
 * Martian origin.
 */
class Martian extends Origin
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->description = 'Born in the Martian Congressional Republic, your '
            . 'life has been influenced by the Martian dream: to terraform the '
            . 'Red Planet into a lush and life-sustaining garden. Like the '
            . 'generations before you, you know that you will likely never see '
            . 'the completion of this work in your lifetime. As a Martian, '
            . 'your character has the following traits:||• Your native gravity '
            . 'is low, the gravity of Mars rather than Earth. Martians are '
            . 'more comfortable with microgravity than Earthers, and better '
            . 'able to tolerate a full 1 g than Belters, operating in-between.';
        $this->name = 'Martian';
    }
}
