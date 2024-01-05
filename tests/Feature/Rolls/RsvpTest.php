<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\User;
use App\Rolls\Rsvp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Tests for RSVPing to an event.
 * @group slack
 * @medium
 */
final class RsvpTest extends TestCase
{
    use RefreshDatabase;

    public function testRsvpDirectlyFromDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $roll = new Rsvp('rsvp foo', 'user', $channel);
        self::assertSame('RSVP is not a valid roll', $roll->forDiscord());
    }

    public function testRsvpDirectlyFromSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make();
        $roll = new Rsvp('rsvp foo', 'user', $channel);
        self::expectException(SlackException::class);
        self::expectExceptionMessage('RSVP is not a valid roll');
        $roll->forSlack();
    }

    public function testRsvpMalformedContent(): void
    {
        Http::fake();

        /** @var Channel */
        $channel = Channel::factory()->make();
        $content = (string)json_encode(['no-actions' => 'test']);
        (new Rsvp($content, 'user', $channel))->handleSlackAction();

        Http::assertNothingSent();
    }

    public function testRsvpWithNotFoundEvent(): void
    {
        Http::fake();

        /** @var Channel */
        $channel = Channel::factory()->make();
        $content = (string)json_encode((object)[
            'actions' => [
                [
                    'action_id' => 'rsvp:0',
                ],
            ],
        ]);
        (new Rsvp($content, 'user', $channel))->handleSlackAction();

        Http::assertNothingSent();
    }

    public function testRsvpWithoutChatUser(): void
    {
        Http::fake();

        /** @var Event */
        $event = Event::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->make();
        $content = (string)json_encode((object)[
            'actions' => [
                [
                    'action_id' => sprintf('rsvp:%d', $event->id),
                ],
            ],
        ]);
        (new Rsvp($content, 'user', $channel))->handleSlackAction();

        Http::assertSent(function (Request $request): bool {
            return 'https://slack.com/api/chat.postEphemeral' === $request->url()
                && 'You don\'t appear to be registered!' === $request['text'];
        });
    }

    public function testRsvpWithoutPermission(): void
    {
        Http::fake();

        $user = User::factory()->create();
        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'banned'])
            ->create();

        /** @var Event */
        $event = Event::factory()->create(['campaign_id' => $campaign->id]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign->id,
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $content = (string)json_encode((object)[
            'actions' => [
                [
                    'action_id' => sprintf('rsvp:%d', $event->id),
                ],
            ],
        ]);
        (new Rsvp($content, 'user', $channel))->handleSlackAction();

        Http::assertSent(function (Request $request): bool {
            return 'https://slack.com/api/chat.postEphemeral' === $request->url()
                && 'You don\'t have permission for that event!' === $request['text'];
        });
    }

    public function testNewRsvp(): void
    {
        Http::fake();

        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();

        /** @var Event */
        $event = Event::factory()->create(['campaign_id' => $campaign->id]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign->id,
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
            'user_id' => $user->id,
        ]);

        $content = (string)json_encode((object)[
            'actions' => [
                [
                    'action_id' => sprintf('rsvp:%d', $event->id),
                    'selected_option' => [
                        'value' => 'accepted',
                    ],
                ],
            ],
        ]);
        (new Rsvp($content, 'username', $channel))->handleSlackAction();

        Http::assertSent(function (Request $request): bool {
            return 'https://slack.com/api/chat.postEphemeral' === $request->url()
                && 'Thanks username, we\'ve recorded your RSVP!' === $request['text'];
        });
        self::assertDatabaseHas(
            'event_rsvps',
            [
                'event_id' => $event->id,
                'response' => 'accepted',
                'user_id' => $user->id,
            ]
        );
    }

    public function testUpdateRsvp(): void
    {
        Http::fake();

        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasAttached($user, ['status' => 'accepted'])
            ->create();

        /** @var Event */
        $event = Event::factory()->create(['campaign_id' => $campaign->id]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign->id,
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
            'user_id' => $user->id,
        ]);

        // User has already declined this event...
        EventRsvp::create([
            'event_id' => $event->id,
            'response' => 'declined',
            'user_id' => $user->id,
        ]);

        // Now they can make it to the event.
        $content = (string)json_encode((object)[
            'actions' => [
                [
                    'action_id' => sprintf('rsvp:%d', $event->id),
                    'selected_option' => [
                        'value' => 'accepted',
                    ],
                ],
            ],
        ]);
        (new Rsvp($content, 'username', $channel))->handleSlackAction();

        Http::assertSent(function (Request $request): bool {
            return 'https://slack.com/api/chat.postEphemeral' === $request->url()
                && 'Thanks username, we\'ve recorded your RSVP!' === $request['text'];
        });
        self::assertDatabaseHas(
            'event_rsvps',
            [
                'event_id' => $event->id,
                'response' => 'accepted',
                'user_id' => $user->id,
            ]
        );
    }
}
