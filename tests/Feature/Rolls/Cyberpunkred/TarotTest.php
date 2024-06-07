<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Cyberpunkred;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Cyberpunkred\TarotDeck;
use App\Rolls\Cyberpunkred\Tarot;
use DB;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Tests drawing a Night City Tarot card in Cyberpunk Red.
 * @group cyberpunkred
 * @medium
 */
final class TarotTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to draw a card in a Slack channel with no campaign.
     * @group slack
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
     * @group discord
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
     */
    public function testDrawFromEmptyDeckDiscord(): void
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

    /**
     * Test trying to draw a card in a IRC channel with a campaign for a
     * different system.
     * @group irc
     */
    public function testIRCTarotCampaignHasWrongSystem(): void
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
            ->forIrc();

        self::assertSame($expected, $response);
    }

    /**
     * Test trying to shuffle a deck in an IRC channel.
     * @group irc
     */
    public function testShuffleDeckIRC(): void
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
            ->forIrc();
        $expected = sprintf('%s shuffled the tarot deck', $channel->username);
        self::assertSame($expected, $response);
    }

    /**
     * Test trying to draw a card in an IRC channel that has never initialized a
     * deck before.
     * @group irc
     */
    public function testDrawCardFromNewDeckIRC(): void
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

        $response = (new Tarot('tarot', $channel->username, $channel))
            ->forIrc();
        self::assertStringContainsString(
            \sprintf('%s drew', $channel->username),
            $response
        );
    }
}
