<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Deck;
use App\Models\StandardDeck;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;
use UnderflowException;

#[Small]
final class StandardDeckTest extends TestCase
{
    /**
     * Test that a new StandardDeck has 52 cards.
     */
    public function testNewDeck(): void
    {
        $deck = new StandardDeck();
        self::assertCount(52, $deck);
    }

    /**
     * Test drawing a card from a new, unshuffled deck.
     */
    public function testDrawingOneFromNewDeck(): void
    {
        $deck = new StandardDeck();
        self::assertSame('A♠', (string)$deck->drawOne());
    }

    /**
     * Test drawing from a shuffled deck.
     */
    public function testDrawFromShuffledDeck(): void
    {
        $deck = new StandardDeck();
        $count = 0;
        while ('A♠' === (string)$deck->peek()) {
            $deck->shuffle();
            ++$count;
            if (5 === $count) {
                self::fail('A♠ on top of deck after 5 shuffles');
            }
        }
        self::assertNotSame('A♠', (string)$deck->drawOne());
    }

    /**
     * Test trying to draw too many cards from a deck.
     */
    public function testDrawTooMany(): void
    {
        $deck = new StandardDeck();
        self::expectException(UnderflowException::class);
        self::expectExceptionMessageIs('Insufficient cards remain in deck');
        $deck->draw(53);
    }

    /**
     * Test emptying the deck and trying to peek at the top card.
     */
    public function testPeekingAtEmptyDeck(): void
    {
        $deck = new StandardDeck();
        $deck->draw(52);
        self::expectException(UnderflowException::class);
        self::expectExceptionMessageIs('Insufficient cards remain in deck');
        $deck->peek();
    }

    /**
     * Test trying to draw an invalid number of cards.
     */
    public function testDrawNegative(): void
    {
        $deck = new StandardDeck();
        self::expectException(RuntimeException::class);
        self::expectExceptionMessageIs('Number of cards must be greater than zero');
        $deck->draw(-1);
    }

    /**
     * Test failing to find a deck in the database.
     */
    public function testDeckNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessageIs('Deck not found');
        StandardDeck::find(0);
    }

    /**
     * Test the database returning an invalid deck type.
     */
    public function testInvalidDeckType(): void
    {
        $id = DB::table('decks')
            ->insertGetId([
                'id' => 1,
                'campaign_id' => Campaign::factory()->create()->id,
                'cards' => 'a:1:{i:0;O:15:"App\Models\Card":2:{s:5:"value";s:1:"2";s:4:"suit";s:3:"♣";}}',
                'type' => 'invalid',
            ]);
        self::expectException(RuntimeException::class);
        self::expectExceptionMessageIs('Deck type not found');
        StandardDeck::find($id);
    }

    /**
     * Test loading a deck from the database.
     */
    public function testLoadDeck(): void
    {
        $id = DB::table('decks')
            ->insertGetId([
                'id' => 1,
                'campaign_id' => Campaign::factory()->create()->id,
                'cards' => 'a:1:{i:0;O:15:"App\Models\Card":2:{s:5:"value";s:1:"2";s:4:"suit";s:3:"♣";}}',
                'type' => StandardDeck::class,
            ]);

        $deck = StandardDeck::find($id);
        self::assertCount(1, $deck);
        self::assertSame('2♣', (string)$deck->drawOne());
    }

    /**
     * Test saving a deck that has no campaign.
     */
    public function testSaveNoCampaign(): void
    {
        $deck = new StandardDeck();
        self::expectException(RuntimeException::class);
        self::expectExceptionMessageIs('Campaign ID must be set to save a deck');
        $deck->save();
    }

    /**
     * Test saving a new deck.
     */
    public function testSaveNewDeck(): void
    {
        $deck = new StandardDeck();
        $deck->campaign_id = Campaign::factory()->create()->id;
        $deck->draw(51);
        self::assertFalse(isset($deck->id));
        $deck->save();
        // @phpstan-ignore staticMethod.alreadyNarrowedType
        self::assertNotNull($deck->id);
    }

    /**
     * Test finding decks for a campaign if the campaign has none.
     */
    public function testFindForCampaignNoDecks(): void
    {
        $campaign = Campaign::factory()->create();
        $decks = StandardDeck::findForCampaign($campaign);
        self::assertEmpty($decks);
    }

    /**
     * Test finding decks for a campaign if the campaign has one.
     */
    public function testFindForDeck(): void
    {
        $campaign = Campaign::factory()->create();
        DB::table('decks')
            ->insertGetId([
                'id' => 1,
                'campaign_id' => $campaign->id,
                'cards' => 'a:1:{i:0;O:15:"App\Models\Card":2:{s:5:"value";s:1:"2";s:4:"suit";s:3:"♣";}}',
                'type' => StandardDeck::class,
            ]);

        $decks = StandardDeck::findForCampaign($campaign);
        self::assertCount(1, $decks);
        self::assertInstanceOf(StandardDeck::class, $decks[0]);
    }

    /**
     * Test truncating the deck table.
     */
    public function testTruncate(): void
    {
        $campaign = Campaign::factory()->create();

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->save();

        self::assertNotSame(0, DB::table('decks')->count());
        Deck::truncate();
        self::assertSame(0, DB::table('decks')->count());
    }
}
