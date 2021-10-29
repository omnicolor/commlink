<?php

declare(strict_types=1);

namespace App\Models;

class Card
{
    /**
     * Create a new card.
     * @param string $value Value of the card (like 2, or K)
     * @param string $suit Suit of the card
     */
    public function __construct(public string $value, public string $suit)
    {
    }

    /**
     * Return the card as a string, like 2♦ for the two of diamonds.
     * @return string
     */
    public function __toString(): string
    {
        return $this->value . $this->suit;
    }
}
