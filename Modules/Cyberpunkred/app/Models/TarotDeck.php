<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use App\Models\Deck;
use Countable;
use RuntimeException;
use UnderflowException;

use function assert;
use function count;

class TarotDeck extends Deck implements Countable
{
    /**
     * Collection of all cards in the deck.
     * @var array<int, TarotCard>
     */
    protected array $allCards;

    /**
     * Collection of remaining cards in the deck.
     * @var array<int, TarotCard>
     */
    public array $currentCards;

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

    /**
     * @return array<int, TarotCard>
     */
    public function draw(?int $number = 1): array
    {
        if ($number <= 0) {
            throw new RuntimeException('Number of cards must be greater than zero');
        }
        if ($number > count($this->currentCards)) {
            throw new UnderflowException('Insufficient cards remain in deck');
        }
        $cards = [];
        while ($number > 0) {
            $card = array_pop($this->currentCards);
            assert($card instanceof TarotCard);
            $cards[] = $card;
            $number--;
        }
        return $cards;
    }

    public function drawOne(): TarotCard
    {
        return $this->draw(1)[0];
    }
}
