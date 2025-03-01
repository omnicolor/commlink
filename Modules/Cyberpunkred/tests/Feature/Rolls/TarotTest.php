<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Rolls;

use App\Models\Campaign;
use App\Models\Channel;
use DB;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Cyberpunkred\Models\TarotDeck;
use Modules\Cyberpunkred\Rolls\Tarot;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Group('cyberpunkred')]
#[Medium]
final class TarotTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to draw a card in a Slack channel with no campaign.
     */
    #[Group('slack')]
    public function testSlackTarotWithNoCampaign(): void
    {
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Tarot decks require a linked Commlink campaign.',
        );
        (new Tarot('tarot', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a card in a Slack channel with a campaign for
     * a different system.
     */
    #[Group('slack')]
    public function testSlackTarotCampaignHasWrongSystem(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Night City Tarot only available for Cyberpunk Red campaigns.',
        );
        (new Tarot('tarot', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a card in a Slack channel with a campaign that
     * hasn't enabled it.
     */
    #[Group('slack')]
    public function testSlackTarotCampaignNotEnabled(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'cyberpunkred']);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Night City Tarot not enabled for campaign.',
        );
        (new Tarot('tarot', $channel->username, $channel))->forSlack();
    }

    /**
     * Test trying to draw a card in a Discord channel with no campaign.
     */
    #[Group('discord')]
    public function testDiscordTarotWithNoCampaign(): void
    {
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
     */
    #[Group('discord')]
    public function testDiscordTarotCampaignHasWrongSystem(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

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
     */
    #[Group('slack')]
    public function testDrawCardFromNewDeck(): void
    {
        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        self::assertDatabaseMissing(
            'decks',
            ['campaign_id' => $campaign->id, 'type' => TarotDeck::class],
        );
        $response = (new Tarot('tarot', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('footer', $response['attachments'][0]);
        self::assertSame(
            '21 cards remain',
            $response['attachments'][0]['footer'],
        );
        self::assertDatabaseHas(
            'decks',
            ['campaign_id' => $campaign->id, 'type' => TarotDeck::class],
        );
    }

    /**
     * Test trying to shuffle a deck in a Slack channel.
     */
    #[Group('slack')]
    public function testShuffleDeckSlack(): void
    {
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

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Tarot('tarot shuffle', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('footer', $response['attachments'][0]);
        self::assertSame(
            '22 cards remain',
            $response['attachments'][0]['footer'],
        );
    }

    /**
     * Test trying to shuffle a deck in a Discord channel.
     */
    #[Group('discord')]
    public function testShuffleDeckDiscord(): void
    {
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
     */
    #[Group('discord')]
    public function testDrawFromEmptyDeckDiscord(): void
    {
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
     */
    #[Group('irc')]
    public function testIRCTarotCampaignHasWrongSystem(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

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
     */
    #[Group('irc')]
    public function testShuffleDeckIRC(): void
    {
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
     */
    #[Group('irc')]
    public function testDrawCardFromNewDeckIRC(): void
    {
        $campaign = Campaign::factory()->create([
            'options' => [
                'nightCityTarot' => true,
            ],
            'system' => 'cyberpunkred',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
        ]);
        $channel->username = $this->faker->name;

        $response = (new Tarot('tarot', $channel->username, $channel))
            ->forIrc();
        self::assertStringContainsString(
            sprintf('%s drew', $channel->username),
            $response,
        );
    }
}
