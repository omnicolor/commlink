<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Enums\ChannelType;
use App\Events\EventCreated;
use App\Listeners\HandleEventCreated;
use App\Models\Campaign;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Medium]
final class HandleEventCreatedTest extends TestCase
{
    public function testHandlingAnEventWithNoChannels(): void
    {
        Http::fake();

        $event = Event::factory()->create();
        $eventCreated = new EventCreated($event);
        self::assertTrue((new HandleEventCreated())->handle($eventCreated));

        Http::assertNothingSent();
    }

    public function testHandlingEventWithSlackChannel(): void
    {
        Http::fake();

        $creator = User::factory()->create();
        $campaign = Campaign::factory()
            ->hasChannels(1, ['type' => ChannelType::Slack->value])
            ->create();

        $event = Event::factory()->create([
            'campaign_id' => $campaign,
            'created_by' => $creator->id,
            'description' => 'Just testing stuff.',
            'name' => 'Test event for Slack notification',
        ]);
        $eventCreated = new EventCreated($event);
        self::assertTrue((new HandleEventCreated())->handle($eventCreated));

        Http::assertSent(function (Request $request) use ($creator): bool {
            return 'https://slack.com/api/chat.postMessage' === $request->url()
                && sprintf('%s scheduled an event', $creator->name) === $request['text'];
        });
    }

    public function testHandlingEventWithDiscordChannelNoWebhook(): void
    {
        Http::fake();

        $creator = User::factory()->create();
        $campaign = Campaign::factory()
            ->hasChannels(1, ['type' => ChannelType::Discord->value])
            ->create();

        $event = Event::factory()->create([
            'campaign_id' => $campaign,
            'created_by' => $creator->id,
        ]);
        $eventCreated = new EventCreated($event);
        self::assertTrue((new HandleEventCreated())->handle($eventCreated));

        Http::assertNothingSent();
    }

    public function testHandlingEventWithDiscordChannelWebhook(): void
    {
        Http::fake();

        $creator = User::factory()->create();
        $campaign = Campaign::factory()
            ->hasChannels(
                1,
                [
                'type' => ChannelType::Discord->value,
                'webhook' => 'https://example.com',
                ]
            )
            ->create();

        $event = Event::factory()->create([
            'campaign_id' => $campaign,
            'created_by' => $creator->id,
            'description' => 'This is an event!',
        ]);
        $eventCreated = new EventCreated($event);
        self::assertTrue((new HandleEventCreated())->handle($eventCreated));

        Http::assertSent(function (Request $request) use ($creator): bool {
            return 'https://example.com' === $request->url()
                && sprintf('%s scheduled an event', $creator->name) === $request['content'];
        });
    }
}
