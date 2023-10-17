<?php

declare(strict_types=1);

namespace App\Models\Capers;

use App\Models\Campaign;
use App\Models\Card;
use App\Models\StandardDeck as BaseDeck;
use Countable;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use stdClass;

class StandardDeck extends BaseDeck implements Countable
{
    /**
     * Character the deck belongs to, null for the GM.
     */
    public ?string $character_id;

    public array $suits = [
        'club' => '♣',
        'diamond' => '♦',
        'heart' => '♥',
        'spade' => '♠',
    ];

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

    public static function findForCampaignAndPlayer(
        Campaign $campaign,
        ?string $characterId
    ): StandardDeck {
        /** @var ?stdClass */
        $row = DB::table('decks')
            ->where('campaign_id', $campaign->id)
            ->where('type', self::class)
            ->where('character_id', $characterId)
            ->first();

        if (null === $row) {
            // No deck found.
            throw new RuntimeException('Deck not found');
        }
        $deck = new self();
        $deck->campaign_id = (int)$row->campaign_id;
        $deck->character_id = $row->character_id;
        $deck->currentCards = unserialize($row->cards);
        $deck->id = (int)$row->id;
        return $deck;
    }

    protected function initialize(): void
    {
        parent::initialize();
        $this->allCards[] = new Card('Bad Joker', '');
        $this->allCards[] = new Card('Good Joker', '');
        $this->currentCards = $this->allCards;
    }

    /**
     * Save a new deck or update an existing one to the database.
     */
    public function save(): StandardDeck
    {
        if (!isset($this->campaign_id)) {
            throw new RuntimeException('Campaign ID must be set to save a deck');
        }
        if (!isset($this->id)) {
            $this->id = DB::table('decks')->insertGetId([
                'campaign_id' => $this->campaign_id,
                'cards' => serialize($this->currentCards),
                'character_id' => $this->character_id,
                'type' => static::class,
            ]);
            return $this;
        }
        DB::table('decks')->where('id', $this->id)
            ->update(['cards' => serialize($this->currentCards)]);
        return $this;
    }
}
