<?php

declare(strict_types=1);

namespace App\Models;

use Countable;

class StandardDeck extends Deck implements Countable
{
    /**
     * @var array<string, string>
     */
    public array $suits = [
        'club' => '♣',
        'diamond' => '♦',
        'heart' => '♥',
        'spade' => '♠',
    ];

    /**
     * @var array<int, string>
     */
    public array $values = [
        2 => '2',
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
        'A',
    ];
}
