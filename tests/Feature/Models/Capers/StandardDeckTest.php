<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Campaign;
use App\Models\Capers\StandardDeck;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Capers decks.
 * @group capers
 * @small
 */
final class StandardDeckTest extends TestCase
{
    /**
     * Test a new StandardDeck.
     * @test
     */
    public function testNewDeck(): void
    {
        $deck = new StandardDeck();
        self::assertCount(54, $deck);
    }

    /**
     * Test trying to load a deck for a character that doesn't have one.
     * @medium
     * @test
     */
    public function testLoadDeckNotFound(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Deck not found');
        StandardDeck::findForCampaignAndPlayer($campaign, 'Bob');
    }

    /**
     * Test trying to load a deck for a character that has one.
     * @medium
     * @test
     */
    public function testLoadDeck(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->character_id = 'Test';
        $deck->drawOne();
        $deck->save();

        $loadedDeck = StandardDeck::findForCampaignAndPlayer($campaign, 'Test');
        self::assertCount(53, $loadedDeck);
    }

    /**
     * Test trying to save a deck with no campaign set.
     * @test
     */
    public function testSaveNoCampaign(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Campaign ID must be set to save a deck');
        (new StandardDeck())->save();
    }

    /**
     * Test trying to update an existing deck.
     * @medium
     * @test
     */
    public function testUpdate(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        $characterId = 'Phil';

        $deck = new StandardDeck();
        $deck->campaign_id = $campaign->id;
        $deck->character_id = $characterId;
        $deck->save();
        $deck->draw(3);
        $deck->save();

        $loadedDeck = StandardDeck::findForCampaignAndPlayer(
            $campaign,
            $characterId
        );
        self::assertSame($deck->id, $loadedDeck->id);
        self::assertCount(51, $loadedDeck);
    }
}
