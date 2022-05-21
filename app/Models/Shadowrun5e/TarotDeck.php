<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use App\Models\Card;
use App\Models\Deck;
use Countable;

class TarotDeck extends Deck implements Countable
{
    /**
     * @var array<string, string>
     */
    public array $suits = [
        'batons' => '♣',
        'cups' => '𐃯',
        'blades' => '†',
        'coins' => '¢',
    ];

    /**
     * @var array<int, string>
     */
    public array $values = [
        1 => 'A',
        '2',
        '3',
        '4',
        '5',
        '6',
        '7',
        '8',
        '9',
        '10',
        'J',
        'Q',
        'K',
    ];

    /**
     * @var array<int, string>
     */
    public array $majorArcana = [
        'The Bastard',
        'The Matrix',
        'The High Priestess',
        'Aes Sídhe Banrìgh',
        'The Chief Executive',
        'The Higher Power',
        'The Avatars',
        'The Ride',
        'Discipline',
        'The Hermit',
        'The Wheel of Fortune',
        'The Vigilante',
        'The Hanged Man',
        '... 404 ...',
        'Threshold',
        'The Dragon',
        'The Tower',
        'The Comet',
        'The Shadows',
        'The Eclipse',
        'Karma',
        'The Awakened World',
    ];

    /**
     * Initialize the cards in a deck.
     */
    protected function initialize(): void
    {
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                $this->allCards[] = new Card($value, $suit);
            }
        }
        foreach ($this->majorArcana as $title) {
            $this->allCards[] = new Card($title, '');
        }
        $this->currentCards = $this->allCards;
    }
}
