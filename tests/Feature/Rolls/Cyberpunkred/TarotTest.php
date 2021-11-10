<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Cyberpunkred;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\CyberpunkRed\TarotDeck;
use App\Rolls\Cyberpunkred\Tarot;
use DB;
use Illuminate\Foundation\Testing\WithFaker;

/**
 * Tests drawing a Night City Tarot card in Cyberpunk Red.
 * @group cyberpunkred
 * @medium
 */
final class TarotTest extends \Tests\TestCase
{
    use WithFaker;

    /**
     * Test trying to draw a card in a Slack channel with no campaign.
     * @group slack
     * @test
     */
    public function testSlackTarotWithNoCampaign(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Tarot decks require a linked Commlink campaign.'
        );
        (new Tarot('tarot', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a card in a Slack channel with a campaign for
     * a different system.
     * @group slack
     * @test
     */
    public function testSlackTarotCampaignHasWrongSystem(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Night City Tarot only available for Cyberpunk Red campaigns.'
        );
        (new Tarot('tarot', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a card in a Slack channel with a campaign that
     * hasn't enabled it.
     * @group slack
     * @test
     */
    public function testSlackTarotCampaignNotEnabled(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'cyberpunkred']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Night City Tarot not enabled for campaign.'
        );
        (new Tarot('tarot', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a card in a Discord channel with no campaign.
     * @group discrd
     * @test
     */
    public function testDiscordTarotWithNoCampaign(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        $expected = 'Tarot decks require a linked Commlink campaign.';
        $response = (new Tarot('tarot', $channel->username, $channel))
            ->forDiscord();

        self::assertSame($expected, $response);
    }

    /**
     * Test trying to draw a card in a Discord channel with a campaign for
     * a different system.
     * @group discord
     * @test
     */
    public function testDiscordTarotCampaignHasWrongSystem(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        $expected = 'Night City Tarot only available for Cyberpunk Red '
            . 'campaigns.';
        $response = (new Tarot('tarot', $channel->username, $channel))
            ->forDiscord();

        self::assertSame($expected, $response);
    }

    /**
     * Test trying to draw a card in a Slack channel that has never initialized
     * a deck before.
     * @group slack
     * @test
     */
    public function testDrawCardFromNewDeck(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        self::assertDatabaseMissing(
            'decks',
            ['campaign_id' => $campaign->id, 'type' => TarotDeck::class]
        );
        $response = (new Tarot('tarot', $channel->username, $channel))
            ->forSlack();
        $response = json_decode((string)$response);
        self::assertSame('21 cards remain', $response->attachments[0]->footer);
        self::assertDatabaseHas(
            'decks',
            ['campaign_id' => $campaign->id, 'type' => TarotDeck::class]
        );
    }

    /**
     * Test trying to shuffle a deck in a Slack channel.
     * @group slack
     * @test
     */
    public function testShuffleDeckSlack(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);

        DB::table('decks')->insert([
            'campaign_id' => $campaign->id,
            'cards' => 'a:0:{}',
            'type' => TarotDeck::class,
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Tarot('tarot shuffle', $channel->username, $channel))
            ->forSlack();
        $response = json_decode((string)$response);
        self::assertSame('22 cards remain', $response->attachments[0]->footer);
    }

    /**
     * Test trying to shuffle a deck in a Discord channel.
     * @group discord
     * @test
     */
    public function testShuffleDeckDiscord(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);

        DB::table('decks')->insert([
            'campaign_id' => $campaign->id,
            'cards' => 'a:0:{}',
            'type' => TarotDeck::class,
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Tarot('tarot shuffle', $channel->username, $channel))
            ->forDiscord();
        $expected = sprintf(
            '**%s shuffled the tarot deck**',
            $channel->username
        );
        self::assertSame($expected, $response);
    }

    /**
     * Test trying to draw a card from an empty deck.
     * @group discord
     * @test
     */
    public function testDrawFromEmptyDeck(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);

        DB::table('decks')->insert([
            'campaign_id' => $campaign->id,
            'cards' => 'a:0:{}',
            'type' => TarotDeck::class,
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        $expected = 'Insufficient cards remain in deck';
        $response = (new Tarot('tarot', $channel->username, $channel))
            ->forDiscord();
        self::assertSame($expected, $response);
    }
}
