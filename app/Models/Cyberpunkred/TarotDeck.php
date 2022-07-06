<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use App\Models\Deck;
use Countable;

class TarotDeck extends Deck implements Countable
{
    /**
     * @var array<int, string>
     */
    public array $majorArcana = [
        'The Fool',
        'The Magician',
        'The High Priestess',
        'The Empress',
        'The Emperor',
        'The Hierophant',
        'The Lovers',
        'The Chariot',
        'Strength',
        'The Hermit',
        'Wheel of Fortune',
        'Justice',
        'The Hanged Man',
        'Death',
        'Temperance',
        'The Devil',
        'The Tower',
        'The Star',
        'The Moon',
        'The Sun',
        'Judgement',
        'The World',
    ];

    protected function initialize(): void
    {
        foreach ($this->majorArcana as $title) {
            $this->allCards[] = new TarotCard($title, '');
        }
        $this->currentCards = $this->allCards;
    }
}
