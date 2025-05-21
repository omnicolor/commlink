<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Enums\ChannelType;
use App\Events\InitiativeAdded;
use App\Listeners\HandleInitiativeEvent;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Initiative;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class HandleInitiativeEventTest extends TestCase
{
    /**
     * Test an event that has no source.
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
     */
    public function testAttachedSlackChannel(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        $campaign = Campaign::factory()
            ->hasChannels(
                1,
                ['type' => ChannelType::Slack->value],
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
     */
    public function testDiscordChannelNoWebhook(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        $campaign = Campaign::factory()
            ->hasChannels(
                1,
                ['type' => 'discord']
            )
            ->create();
        $event = new InitiativeAdded($initiative, $campaign);
        (new HandleInitiativeEvent())->handle($event);

        Http::assertNothingSent();
    }

    /**
     * Test an event that has an attached Discord channel.
     */
    public function testAttachedDiscordChannel(): void
    {
        Http::fake();

        $initiative = new Initiative([
            'channel_id' => 'C123',
            'character_name' => 'Conan',
            'initiative' => 9,
        ]);

        $campaign = Campaign::factory()
            ->hasChannels(
                1,
                [
                    'type' => ChannelType::Discord->value,
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
