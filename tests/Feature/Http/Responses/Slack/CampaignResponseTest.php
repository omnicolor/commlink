<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Responses\Slack;

use App\Events\ChannelLinked;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\CampaignResponse;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

/**
 * @group slack
 * @medium
 */
final class CampaignResponseTest extends \Tests\TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test not including a channel.
     * @test
     */
    public function testNoChannel(): void
    {
        Event::fake();
        Http::fake();

        self::expectException(SlackException::class);
        self::expectExceptionMessage('Channel is required');
        new CampaignResponse('campaign 6', Response::HTTP_OK, []);

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to link a campaign without including the campaign ID.
     * @test
     */
    public function testLinkWithoutCampaignId(): void
    {
        Event::fake();
        Http::fake();

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'To link a channel, use `campaign <campaignId>`.'
        );
        new CampaignResponse('campaign', Response::HTTP_OK, [], new Channel());

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to link a campaign to a channel that already has one.
     * @test
     */
    public function testLinkAlreadyLinkedChannel(): void
    {
        Event::fake();
        Http::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->for($campaign)->create();
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'This channel is already registered for "%s".',
            // @phpstan-ignore-next-line
            $channel->campaign->name
        ));
        new CampaignResponse('campaign 5', Response::HTTP_OK, [], $channel);

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to link a campaign to an unlinked channel, but the user has
     * not linked their user.
     * @test
     */
    public function testLinkNoChatUser(): void
    {
        Event::fake();
        Http::fake();

        /** @var Channel */
        $channel = Channel::factory()->make();
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'You must have already created an account on <%s|%s> and '
                . 'linked it to this server before you can register a '
                . 'channel to a campaign.',
            config('app.url'),
            config('app.name')
        ));
        new CampaignResponse('campaign 5', Response::HTTP_OK, [], $channel);

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to link a campaign that isn't found.
     * @test
     */
    public function testLinkNotFoundCampaign(): void
    {
        Event::fake();
        Http::fake();

        /** @var Channel */
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(5);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => true,
        ]);
        self::expectException(SlackException::class);
        self::expectExceptionMessage('No campaign was found for ID "0".');
        new CampaignResponse('campaign 0', Response::HTTP_OK, [], $channel);

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to link to a campaign that you neither registered nor GM.
     * @test
     */
    public function testLinkNotOwnedCampaign(): void
    {
        Event::fake();
        Http::fake();

        /** @var Channel */
        $channel = Channel::factory()->make();
        $channel->user = \Str::random(5);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => true,
        ]);
        /** @var Campaign */
        $campaign = Campaign::factory()->create();
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have created the campaign or be the GM to link a Slack '
                . 'channel.'
        );
        new CampaignResponse(
            sprintf('campaign %d', $campaign->id),
            Response::HTTP_OK,
            [],
            $channel
        );

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test trying to link a campaign to a channel with incompatible systems.
     * @test
     */
    public function testLinkingIncompatibleCampaign(): void
    {
        Event::fake();
        Http::fake();

        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'dnd5e',
        ]);
        $channel->user = \Str::random(5);
        /** @var User */
        $user = User::factory()->create();
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => true,
            'user_id' => $user,
        ]);
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
        ]);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'The channel is already registered to play %s. "%s" is playing %s.',
            $channel->getSystem(),
            $campaign->name,
            $campaign->getSystem()
        ));
        new CampaignResponse(
            sprintf('campaign %d', $campaign->id),
            Response::HTTP_OK,
            [],
            $channel
        );

        Event::assertNotDispatched(ChannelLinked::class);
        Http::assertNothingSent();
    }

    /**
     * Test linking a campaign that has never been registered before.
     * @test
     */
    public function testLinkNewChannel(): void
    {
        Event::fake();

        $channel = new Channel();
        $channel->channel_id = \Str::random(10);
        $channel->registered_by = null;
        $channel->server_id = \Str::random(10);
        $channel->type = Channel::TYPE_SLACK;
        $channel->user = \Str::random(5);
        $channel->username = $this->faker->name;

        /** @var User */
        $user = User::factory()->create();
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => true,
            'user_id' => $user,
        ]);
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
        ]);

        $teamResponse = Http::response(
            [
                'ok' => true,
                'teams' => [
                    [
                        'id' => $channel->server_id,
                        'name' => 'Team Name',
                    ],
                ],
            ],
            Response::HTTP_OK,
            []
        );
        $channelResponse = Http::response(
            [
                'ok' => true,
                'channel' => [
                    'name' => 'Channel Name',
                ],
            ],
            Response::HTTP_OK,
            []
        );
        Http::fake([
            'https://slack.com/api/auth.teams.list' => $teamResponse,
            'https://slack.com/api/conversations.info?channel=' . urlencode($channel->channel_id) => $channelResponse,
            '*' => Http::response([], Response::HTTP_BAD_REQUEST, []),
        ]);

        $response = json_decode((string)(new CampaignResponse(
            sprintf('campaign %d', $campaign->id),
            Response::HTTP_OK,
            [],
            $channel
        )));
        self::assertSame('in_channel', $response->response_type);
        self::assertSame('Registered', $response->attachments[0]->title);
        self::assertSame(
            sprintf(
                '%s has registered this channel for the "%s" campaign, playing %s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem()
            ),
            $response->attachments[0]->text
        );

        Http::assertSent(function (Request $request): bool {
            $urls = [
                'https://slack.com/api/auth.teams.list',
                'https://slack.com/api/conversations.info',
            ];
            return in_array($request->url(), $urls, true);
        });
        Event::assertDispatched(function (ChannelLinked $event) use ($campaign): bool {
            $channel = $event->broadcastWith();
            return $campaign->name === $channel['campaign_name']
                && 'Team Name' === $event->channel->server_name
                && 'Channel Name' === $event->channel->channel_name;
        });
    }

    /**
     * Test linking a channel that had previously been registered for the same
     * system as the campaign.
     * @test
     */
    public function testLinkRegisteredChannel(): void
    {
        Event::fake();
        Http::fake();

        /** @var User */
        $user = User::factory()->create();
        /** @var Channel */
        $channel = Channel::factory()->create([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = \Str::random(5);
        $channel->username = $this->faker->name;

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => $channel->type,
            'verified' => true,
            'user_id' => $user,
        ]);
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'registered_by' => $user,
            'system' => 'shadowrun5e',
        ]);

        Http::fake([]);

        $response = json_decode((string)(new CampaignResponse(
            sprintf('campaign %d', $campaign->id),
            Response::HTTP_OK,
            [],
            $channel
        )));
        self::assertSame('in_channel', $response->response_type);
        self::assertSame('Registered', $response->attachments[0]->title);
        self::assertSame(
            sprintf(
                '%s has registered this channel for the "%s" campaign, playing %s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem()
            ),
            $response->attachments[0]->text
        );

        Event::assertNotDispatched(ChannelLinked::class);
        // Channel was already registered, so it should already have Team and
        // Channels named.
        Http::assertNothingSent();
    }
}
