<?php

declare(strict_types=1);

namespace Tests\Feature\Listeners;

use App\Events\EventCreated;
use App\Listeners\HandleEventCreated;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

use function sprintf;

/**
 * @medium
 */
final class HandleEventCreatedTest extends TestCase
{
    use RefreshDatabase;

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
        /** @var Channel */
        // @phpstan-ignore-next-line
        $campaign = Campaign::factory()
            ->hasChannels(1, ['type' => Channel::TYPE_SLACK])
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
        /** @var Channel */
        // @phpstan-ignore-next-line
        $campaign = Campaign::factory()
            ->hasChannels(1, ['type' => Channel::TYPE_DISCORD])
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
        /** @var Channel */
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
