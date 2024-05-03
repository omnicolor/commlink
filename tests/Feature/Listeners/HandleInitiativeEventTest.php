<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\InitiativeAdded;
use App\Listeners\HandleInitiativeEvent;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Initiative;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Tests for handling a character rolling their initiative.
 * @medium
 */
final class HandleInitiativeEventTest extends TestCase
{
    /**
     * Test an event that has no source.
     * @test
     */
    public function testNoSource(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        /** @var Campaign */
        $campaign = Campaign::factory()->create();

        $event = new InitiativeAdded($initiative, $campaign);
        (new HandleInitiativeEvent())->handle($event);

        Http::assertNothingSent();
    }

    /**
     * Test an event that has a source that is the same as the only registered
     * channel.
     * @test
     */
    public function testSameSource(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
        ]);

        $event = new InitiativeAdded($initiative, $campaign, $channel);
        (new HandleInitiativeEvent())->handle($event);

        Http::assertNothingSent();
    }

    /**
     * Test an event that has an attached Slack channel.
     * @test
     */
    public function testAttachedSlackChannel(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        // @phpstan-ignore-next-line
        $campaign = Campaign::factory()
            ->hasChannels(
                1,
                ['type' => Channel::TYPE_SLACK]
            )
            ->create();
        $event = new InitiativeAdded($initiative, $campaign);
        (new HandleInitiativeEvent())->handle($event);

        Http::assertSent(function (Request $request): bool {
            return 'https://slack.com/api/chat.postMessage' === $request->url();
        });
    }

    /**
     * Test an event that has an attached Discord channel but no webhook.
     * @test
     */
    public function testDiscordChannelNoWebhook(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        // @phpstan-ignore-next-line
        $campaign = Campaign::factory()
            ->hasChannels(
                1,
                [
                    'type' => Channel::TYPE_DISCORD,
                    'webhook' => null,
                ]
            )
            ->create();
        $event = new InitiativeAdded($initiative, $campaign);
        (new HandleInitiativeEvent())->handle($event);

        Http::assertNothingSent();
    }

    /**
     * Test an event that has an attached Discord channel.
     * @test
     */
    public function testAttachedDiscordChannel(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        // @phpstan-ignore-next-line
        $campaign = Campaign::factory()
            ->hasChannels(
                1,
                [
                    'type' => Channel::TYPE_DISCORD,
                    'webhook' => 'https://example.com',
                ]
            )
            ->create();
        $event = new InitiativeAdded($initiative, $campaign);
        (new HandleInitiativeEvent())->handle($event);

        Http::assertSent(function (Request $request): bool {
            return 'https://example.com' === $request->url();
        });
    }
}
