<?php

declare(strict_types=1);

namespace App\Models;

use Countable;
use Error;
use Illuminate\Support\Facades\DB;
use Override;
use RuntimeException;
use UnderflowException;

use function array_pop;
use function assert;
use function count;
use function end;
use function serialize;
use function shuffle;
use function unserialize;

/**
 * @property ?int $campaign_id
 * @property ?int $id
 */
abstract class Deck implements Countable
{
    /**
     * Collection of all cards in the deck.
     * @var array<int, Card>
     */
    protected array $allCards;

    /**
     * Campaign the deck belongs to.
     */
    public int $campaign_id;

    /**
     * Collection of remaining cards in the deck.
     * @var array<int, Card>
     */
    public array $currentCards;

    /**
     * Database ID of the deck.
     */
    public int $id;

    /**
     * Collection of valid suits for Cards in the deck.
     *
     * ['name' => 'glyph']
     * @var array<string, string>
     */
    public array $suits;

    /**
     * Collection of valid values for Cards in the deck.
     *
     * ['numeric value' => 'card value']
     * @var array<int, string>
     */
    public array $values;

    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Return the number of cards remaining in the deck.
     */
    #[Override]
    public function count(): int
    {
        return count($this->currentCards);
    }

    /**
     * Draw one or more cards from the deck.
     * @return array<int, Card>
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
            assert($card instanceof Card);
            $cards[] = $card;
            $number--;
        }
        return $cards;
    }

    /**
     * Return the top card from the deck.
     */
    public function drawOne(): Card
    {
        return $this->draw(1)[0];
    }

    /**
     * Find a deck in the database.
     * @throws RuntimeException
     */
    public static function find(int $id): Deck
    {
        /** @var object{type: string, campaign_id: int, cards: string} */
        $row = DB::table('decks')->find($id);
        if (null === $row) {
            throw new RuntimeException('Deck not found');
        }
        try {
            /** @var Deck */
            $deck = new $row->type();
        } catch (Error) {
            throw new RuntimeException('Deck type not found');
        }

        $deck->campaign_id = $row->campaign_id;
        $deck->currentCards = unserialize($row->cards);
        $deck->id = $id;
        return $deck;
    }

    /**
     * Find all decks for a campaign.
     * @return array<int, Deck>
     */
    public static function findForCampaign(Campaign $campaign): array
    {
        $rows = DB::table('decks')
            ->where('campaign_id', $campaign->id)
            ->get();
        $decks = [];
        foreach ($rows as $row) {
            try {
                /** @var Deck */
                $deck = new $row->type();
            } catch (Error) {
                continue;
            }
            $deck->campaign_id = (int)$row->campaign_id;
            $deck->currentCards = unserialize($row->cards);
            $deck->id = (int)$row->id;
            $decks[] = $deck;
        }
        return $decks;
    }

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
        $this->currentCards = $this->allCards;
    }

    /**
     * Take a peek at the top card in the deck without removing it.
     */
    public function peek(): Card
    {
        if (0 === count($this->currentCards)) {
            throw new UnderflowException('Insufficient cards remain in deck');
        }
        return end($this->currentCards);
    }

    /**
     * Save a new deck or update an existing one to the database.
     */
    public function save(): Deck
    {
        if (!isset($this->campaign_id)) {
            throw new RuntimeException('Campaign ID must be set to save a deck');
        }
        if (!isset($this->id)) {
            $this->id = DB::table('decks')->insertGetId([
                'campaign_id' => $this->campaign_id,
                'cards' => serialize($this->currentCards),
                'type' => static::class,
            ]);
            return $this;
        }
        DB::table('decks')->where('id', $this->id)
            ->update(['cards' => serialize($this->currentCards)]);
        return $this;
    }

    /**
     * Reset and randomize the deck.
     */
    public function shuffle(): Deck
    {
        $this->currentCards = $this->allCards;
        shuffle($this->currentCards);
        return $this;
    }

    /**
     * Delete all decks from the database.
     */
    public static function truncate(): void
    {
        DB::table('decks')->truncate();
    }
}
