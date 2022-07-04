<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Campaign;
use App\Models\Deck;
use App\Models\StandardDeck;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

/**
 * @small
 */
final class StandardDeckTest extends TestCase
{
    /**
     * Test that a new StandardDeck has 52 cards.
     * @test
     */
    public function testNewDeck(): void
    {
        $deck = new StandardDeck();
        self::assertCount(52, $deck);
    }

    /**
     * Test drawing a card from a new, unshuffled deck.
     * @test
     */
    public function testDrawingOneFromNewDeck(): void
    {
        $deck = new StandardDeck();
        self::assertSame('A♠', (string)$deck->drawOne());
    }

    /**
     * Test drawing from a shuffled deck.
     * @test
     */
    public function testDrawFromShuffledDeck(): void
    {
        $deck = new StandardDeck();
        $count = 0;
        while ('A♠' === (string)$deck->peek()) {
            $deck->shuffle();
            $count++;
            if (5 === $count) {
                self::fail('A♠ on top of deck after 5 shuffles');
            }
        }
        self::assertNotSame('A♠', (string)$deck->drawOne());
    }

    /**
     * Test trying to draw too many cards from a deck.
     * @test
     */
    public function testDrawTooMany(): void
    {
        $deck = new StandardDeck();
        self::expectException(\UnderflowException::class);
        self::expectExceptionMessage('Insufficient cards remain in deck');
        $deck->draw(53);
    }

    /**
     * Test emptying the deck and trying to peek at the top card.
     * @test
     */
    public function testPeekingAtEmptyDeck(): void
    {
        $deck = new StandardDeck();
        $deck->draw(52);
        self::expectException(\UnderflowException::class);
        self::expectExceptionMessage('Insufficient cards remain in deck');
        $deck->peek();
    }

    /**
     * Test trying to draw an invalid number of cards.
     * @test
     */
    public function testDrawNegative(): void
    {
        $deck = new StandardDeck();
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Number of cards must be greater than zero');
        $deck->draw(-1);
    }

    /**
     * Test failing to find a deck in the database.
     * @test
     */
    public function testDeckNotFound(): void
    {
        DB::shouldReceive('table->find')
            ->with(1)
            ->andReturn(null);
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Deck not found');
        StandardDeck::find(1);
    }

    /**
     * Test the database returning an invalid deck type.
     * @test
     */
    public function testInvalidDeckType(): void
    {
        DB::shouldReceive('table->find')
            ->with(1)
            ->andReturn((object)['type' => 'Invalid']);
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Deck type not found');
        StandardDeck::find(1);
    }

    /**
     * Test loading a deck from the database.
     * @test
     */
    public function testLoadDeck(): void
    {
        DB::shouldReceive('table->find')
            ->with(1)
            ->andReturn((object)[
                'id' => 1,
                'campaign_id' => 1,
                'cards' => 'a:1:{i:0;O:15:"App\Models\Card":2:{s:5:"value";s:1:"2";s:4:"suit";s:3:"♣";}}',
                'type' => StandardDeck::class,
            ]);

        $deck = StandardDeck::find(1);
        self::assertCount(1, $deck);
        self::assertSame('2♣', (string)$deck->drawOne());
    }

    /**
     * Test saving a deck that has no campaign.
     * @test
     */
    public function testSaveNoCampaign(): void
    {
        $deck = new StandardDeck();
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Campaign ID must be set to save a deck');
        $deck->save();
    }

    /**
     * Test saving a new deck.
     * @test
     */
    public function testSaveNewDeck(): void
    {
        DB::shouldReceive('table->insertGetId')
            ->with([
                'campaign_id' => 13,
                'cards' => 'a:1:{i:0;O:15:"App\Models\Card":2:{s:5:"value";s:1:"2";s:4:"suit";s:3:"♣";}}',
                'type' => StandardDeck::class,
            ])
            ->andReturn(42);
        $deck = new StandardDeck();
        $deck->campaign_id = 13;
        $deck->draw(51);
        self::assertFalse(isset($deck->id));
        $deck->save();
        self::assertSame(42, $deck->id);
    }

    /**
     * Test updating an existing deck.
     * @test
     */
    public function testUpdateDeck(): void
    {
        DB::shouldReceive('table->where->update')
            ->with([
                'cards' => 'a:1:{i:0;O:15:"App\Models\Card":2:{s:5:"value";s:1:"2";s:4:"suit";s:3:"♣";}}',
            ]);
        $deck = new StandardDeck();
        $deck->campaign_id = 13;
        $deck->id = 42;
        $deck->draw(51);
        $deck->save();
    }

    /**
     * Test finding decks for a campaign if the campaign has none.
     * @medium
     * @test
     */
    public function testFindForCampaignNoDecks(): void
    {
        DB::shouldReceive('table->where->get')->andReturn([]);

        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $decks = StandardDeck::findForCampaign($campaign);
        self::assertEmpty($decks);
    }

    /**
     * Test finding decks for a campaign if the campaign has one.
     * @medium
     * @test
     */
    public function testFindForDeck(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        DB::shouldReceive('table->where->get')
            ->andReturn([
                (object)[
                    'id' => 1,
                    'campaign_id' => $campaign->id,
                    'cards' => 'a:1:{i:0;O:15:"App\Models\Card":2:{s:5:"value";s:1:"2";s:4:"suit";s:3:"♣";}}',
                    'type' => StandardDeck::class,
                ],
                (object)[
                    'type' => '\App\Models\UnknownDeck',
                ],
            ]);

        $decks = StandardDeck::findForCampaign($campaign);
        self::assertCount(1, $decks);
        self::assertInstanceOf(StandardDeck::class, $decks[0]);
    }

    /**
     * Test truncating the deck table.
     * @medium
     * @test
     */
    public function testTruncate(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->save();

        self::assertNotSame(0, DB::table('decks')->count());
        Deck::truncate();
        self::assertSame(0, DB::table('decks')->count());
    }
}
