<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Events\ChannelLinked;
use App\Models\Campaign as CampaignModel;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\User;
use App\Rolls\Campaign as CampaignRoll;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Sections\Text;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Group('campaigns')]
#[Medium]
final class CampaignTest extends TestCase
{
    use WithFaker;

    /**
     * Test trying to link a campaign to a Slack channel without the ID.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignNoId(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['type' => ChannelType::Slack]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'To link a campaign to this channel, use `campaign <campaignId>`.'
        );

        (new CampaignRoll('campaign', 'username', $channel))->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Discord channel without the ID.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignNoId(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['type' => ChannelType::Discord]);

        self::assertSame(
            'To link a campaign to this channel, use `campaign <campaignId>`.',
            (new CampaignRoll('campaign', 'username', $channel))->forDiscord()
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    #[Group('irc')]
    public function testIrcError(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['type' => ChannelType::Irc]);

        self::assertSame(
            'To link a campaign to this channel, use `campaign <campaignId>`.',
            (new CampaignRoll('campaign', 'username', $channel))->forIrc()
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Slack channel if the channel already
     * has one.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignRedundant(): void
    {
        Event::fake();

        $campaign = CampaignModel::factory()->create();

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'This channel is already registered for "%s".',
            $campaign->name,
        ));

        (new CampaignRoll('campaign 9999', 'username', $channel))->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Discord channel if the channel
     * already has one.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignRedundant(): void
    {
        Event::fake();

        $campaign = CampaignModel::factory()->create();

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'type' => ChannelType::Discord,
        ]);

        $expected = sprintf(
            'This channel is already registered for "%s".',
            $campaign->name,
        );
        self::assertSame(
            $expected,
            (new CampaignRoll('campaign 1', 'username', $channel))->forDiscord(),
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Slack channel if the user isn't
     * registered.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignNoChatUser(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['type' => ChannelType::Slack]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'You must have already created an account on %s (%s) and '
                . 'linked it to this server before you can register a '
                . 'channel to a campaign.',
            config('app.name'),
            config('app.url'),
        ));

        (new CampaignRoll('campaign 9999', 'username', $channel))->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Discord channel if the user isn't
     * registered.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignNoChatUser(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['type' => ChannelType::Discord]);

        $expected = sprintf(
            'You must have already created an account on %s (%s) and '
                . 'linked it to this server before you can register a '
                . 'channel to a campaign.',
            config('app.name'),
            config('app.url'),
        );

        self::assertSame(
            $expected,
            (new CampaignRoll('campaign 9', 'username', $channel))->forDiscord()
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Slack channel if the campaign isn't
     * found.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignNoCampaign(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['type' => ChannelType::Slack]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage('No campaign was found for ID "9999".');

        (new CampaignRoll('campaign 9999', 'username', $channel))->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Discord channel if the campaign isn't
     * found.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignNoCampaign(): void
    {
        Event::fake();

        $channel = new Channel([
            'channel_id' => 'C' . Str::random(10),
            'server_id' => 'T' . Str::random(10),
            'type' => ChannelType::Discord,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        self::assertSame(
            'No campaign was found for ID "0".',
            (new CampaignRoll('campaign 0', 'username', $channel))->forDiscord()
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Slack channel if the campaign is for
     * a different system.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignDifferentSystem(): void
    {
        Event::fake();

        $campaign = CampaignModel::factory()->create(['system' => 'dnd5e']);

        $channel = Channel::factory()->make([
            'system' => 'shadowrun5e',
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(sprintf(
            'The channel is already registered to play Shadowrun 5th Edition. '
            . '"%s" is playing Dungeons & Dragons 5th Edition.',
            $campaign->name,
        ));

        (new CampaignRoll(
            sprintf('campaign %s', $campaign->id),
            'username',
            $channel
        ))
            ->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Discord channel if the campaign is
     * playing a different system.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignDifferentSystem(): void
    {
        Event::fake();

        $campaign = CampaignModel::factory()->create([
            'system' => 'shadowrun5e',
        ]);

        $channel = Channel::factory()->make([
            'system' => 'cyberpunkred',
            'type' => ChannelType::Discord,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $expected = sprintf(
            'The channel is already registered to play Cyberpunk Red. '
                . '"%s" is playing Shadowrun 5th Edition.',
            $campaign->name,
        );
        self::assertSame(
            $expected,
            (new CampaignRoll(
                sprintf('campaign %d', $campaign->id),
                'username',
                $channel
            ))->forDiscord()
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Slack channel if the user doesn't
     * have permission to do so.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignPermissionDenied(): void
    {
        Event::fake();

        $campaign = CampaignModel::factory()->create();

        $channel = Channel::factory()->make([
            'system' => $campaign->system,
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have created the campaign or be the GM to link a Slack '
                . 'channel.'
        );

        (new CampaignRoll(
            sprintf('campaign %s', $campaign->id),
            'username',
            $channel
        ))
            ->forSlack();

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test trying to link a campaign to a Discord channel if the user doesn't
     * have permission to do so.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignPermissionDenied(): void
    {
        Event::fake();

        $campaign = CampaignModel::factory()->create();

        $channel = Channel::factory()->make([
            'system' => $campaign->system,
            'type' => ChannelType::Discord,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $expected = 'You must have created the campaign or be the GM to link a '
            . 'Slack channel.';
        self::assertSame(
            $expected,
            (new CampaignRoll(
                sprintf('campaign %d', $campaign->id),
                'username',
                $channel
            ))->forDiscord()
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test linking a campaign to a new Slack channel.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignSuccessNewChannel(): void
    {
        Event::fake();

        $team_id = 'T' . Str::random(10);
        $teams = [
            'ok' => true,
            'teams' => [
                [
                    'id' => $team_id,
                    'name' => 'foo',
                ],
            ],
        ];
        $teams_response = Http::response($teams, Response::HTTP_OK);

        $channel_id = 'C' . Str::random(10);
        $channels = [
            'ok' => true,
            'channel' => [
                'id' => $channel_id,
                'name' => 'Channel name',
            ],
        ];
        $channels_response = Http::response($channels, Response::HTTP_OK);
        $channels_url = 'https://slack.com/api/conversations.info?channel='
            . $channel_id;
        Http::fake([
            'https://slack.com/api/auth.teams.list' => $teams_response,
            $channels_url => $channels_response,
        ]);

        $user = User::factory()->create();

        $campaign = CampaignModel::factory()->create([
            'registered_by' => $user->id,
        ]);

        $channel = new Channel([
            'channel_id' => $channel_id,
            'server_id' => $team_id,
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);
        $channel->username = $this->faker->name;

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new CampaignRoll(
            sprintf('campaign %s', $campaign->id),
            'username',
            $channel,
        ))->forSlack()
            ->jsonSerialize();
        self::assertCount(2, $response['blocks']);
        self::assertEquals(
            (new Header('Registered'))->jsonSerialize(),
            $response['blocks'][0],
        );
        self::assertEquals(
            (new Text(sprintf(
                '%s has registered this channel for the "%s" campaign, playing %s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem(),
            )))->jsonSerialize(),
            $response['blocks'][1],
        );

        Event::assertDispatched(ChannelLinked::class);
    }

    /**
     * Test linking a campaign to a new Discord channel.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignSuccessNewChannel(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $campaign = CampaignModel::factory()->create(['gm' => $user->id]);

        $channel = new Channel([
            'channel_id' => 'C' . Str::random(10),
            'server_id' => 'T' . Str::random(10),
            'type' => ChannelType::Discord,
        ]);
        $channel->user = 'U' . Str::random(10);
        $channel->username = $this->faker->name;

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new CampaignRoll(
            sprintf('campaign %d', $campaign->id),
            'username',
            $channel
        ))->forDiscord();

        self::assertSame(
            sprintf(
                '%s has registered this channel for the "%s" campaign, playing %s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem(),
            ),
            $response
        );

        Event::assertDispatched(function (ChannelLinked $event) use ($campaign): bool {
            $channel = $event->broadcastWith();
            return $channel['campaign_name'] === $campaign->name;
        });
    }

    /**
     * Test linking a campaign to an old Slack channel.
     */
    #[Group('slack')]
    public function testSlackLinkCampaignSuccessOldChannel(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $campaign = CampaignModel::factory()->create([
            'registered_by' => $user->id,
        ]);

        $channel = Channel::factory()->make([
            'registered_by' => $user->id,
            'system' => $campaign->system,
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);
        $channel->username = $this->faker->name;

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new CampaignRoll(
            sprintf('campaign %s', $campaign->id),
            'username',
            $channel
        ))->forSlack()
            ->jsonSerialize();
        self::assertCount(2, $response['blocks']);
        self::assertEquals(
            (new Header('Registered'))->jsonSerialize(),
            $response['blocks'][0],
        );
        self::assertEquals(
            (new Text(sprintf(
                '%s has registered this channel for the "%s" campaign, playing %s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem(),
            )))->jsonSerialize(),
            $response['blocks'][1],
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    /**
     * Test linking a campaign to an old Discord channel.
     */
    #[Group('discord')]
    public function testDiscordLinkCampaignSuccessOldChannel(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $campaign = CampaignModel::factory()->create(['gm' => $user->id]);

        $channel = Channel::factory()->make([
            'registered_by' => 1,
            'system' => $campaign->system,
            'type' => ChannelType::Discord,
        ]);
        $channel->user = 'U' . Str::random(10);
        $channel->username = $this->faker->name;

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new CampaignRoll(
            sprintf('campaign %d', $campaign->id),
            'username',
            $channel,
        ))->forDiscord();

        self::assertSame(
            sprintf(
                '%s has registered this channel for the "%s" campaign, playing %s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem(),
            ),
            $response
        );

        Event::assertNotDispatched(ChannelLinked::class);
    }

    #[Group('irc')]
    public function testLinkIrc(): void
    {
        Event::fake();

        $user = User::factory()->create();

        $campaign = CampaignModel::factory()->create(['gm' => $user->id]);
        $channel = new Channel([
            'channel_id' => 'C' . Str::random(10),
            'server_id' => 'T' . Str::random(10),
            'type' => ChannelType::Irc,
        ]);
        $channel->user = 'U' . Str::random(10);
        $channel->username = $this->faker->name;

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new CampaignRoll(
            sprintf('campaign %d', $campaign->id),
            'username',
            $channel
        ))->forIrc();

        self::assertSame(
            sprintf(
                '%s has registered this channel for the "%s" campaign, playing %s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem(),
            ),
            $response
        );

        Event::assertDispatched(function (ChannelLinked $event) use ($campaign): bool {
            $channel = $event->broadcastWith();
            return $channel['campaign_name'] === $campaign->name;
        });
    }
}
