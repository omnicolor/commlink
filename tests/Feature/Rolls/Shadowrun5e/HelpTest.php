<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use App\Rolls\Shadowrun5e\Help;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Tests for getting help in a Channel registered as Shadowrun 5E.
 * @group discord
 * @group shadowrun5e
 * @group slack
 * @medium
 */
final class HelpTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getting help via Slack.
     * @test
     */
    public function testHelpSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            'Commlink - Shadowrun 5th Edition',
            $response->attachments[0]->title
        );
        self::assertSame(
            'No character linked' . \PHP_EOL
                . '· `link <characterId>` - Link a character to this channel'
                . \PHP_EOL
                . '· `init 12+3d6` - Roll your initiative' . \PHP_EOL,
            $response->attachments[1]->text
        );
    }

    /**
     * Test getting help via Discord.
     * @test
     */
    public function testHelpDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make([
            'type' => Channel::TYPE_DISCORD,
        ]);
        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            'Commlink - Shadowrun 5th Edition',
            $response
        );
        self::assertStringContainsString(
            'No character linked',
            $response
        );
    }

    /**
     * Test getting help in a channel that is attached to a campaign, but the
     * current user isn't registered.
     * @test
     */
    public function testHelpWithCampaignNoChatUser(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'system' => 'shadowrun5e',
        ]);
        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            'No character linked' . \PHP_EOL
                . '· `link <characterId>` - Link a character to this channel'
                . \PHP_EOL
                . '· `init 12+3d6` - Roll your initiative' . \PHP_EOL,
            $response->attachments[1]->text
        );
    }

    /**
     * Test getting help in a channel that is attached to a campaign, but the
     * current user, while linked, isn't the GM.
     * @test
     */
    public function testHelpWithCampaignWithChatUserNotGM(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => null,
            'system' => 'shadowrun5e',
        ]);
        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            'No character linked' . \PHP_EOL
                . '· `link <characterId>` - Link a character to this channel'
                . \PHP_EOL
                . '· `init 12+3d6` - Roll your initiative' . \PHP_EOL,
            $response->attachments[1]->text
        );
    }

    /**
     * Test getting help in a channel that is attached to a campaign, but the
     * current user, while linked, isn't the GM.
     * @test
     */
    public function testHelpWithCampaignWithChatUserWithCharacter(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => null,
            'system' => 'shadowrun5e',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'body' => 1,
            'charisma' => 2,
            'edge' => 6,
            'intuition' => 3,
            'system' => 'shadowrun5e',
            'reaction' => 4,
            'strength' => 5,
            'willpower' => 6,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            \sprintf(
                'You\'re playing %s in this channel' . \PHP_EOL
                    . '· `composure` - Make a composure roll (8)' . \PHP_EOL
                    . '· `judge` - Make a judge intentions check (5)' . \PHP_EOL
                    . '· `lift` - Make a lift/carry roll (6)' . \PHP_EOL
                    . '· `memory` - Make a memory test (6)' . \PHP_EOL
                    . '· `soak` - Make a soak test (1)' . \PHP_EOL
                    . '· `luck` - Make a luck (edge) test (6)' . \PHP_EOL
                    . '· `init` - Roll your initiative (1d6+7)' . \PHP_EOL
                    . '· `push 6 [limit] [text]` - Push the limit with 6 + your edge (6)' . \PHP_EOL
                    . '· `blitz` - Blitz initiative (5d6+7)' . \PHP_EOL,
                (string)$character,
            ),
            $response->attachments[1]->text
        );
        $character->delete();
    }

    /**
     * Test getting help in a channel that is attached to a campaign and
     * a linked technomancer.
     * @test
     */
    public function testHelpForTechnomancer(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => null,
            'system' => 'shadowrun5e',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'body' => 1,
            'charisma' => 2,
            'edge' => 6,
            'intuition' => 3,
            'system' => 'shadowrun5e',
            'reaction' => 4,
            'resonance' => 5,
            'strength' => 5,
            'willpower' => 6,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            \sprintf(
                'You\'re playing %s in this channel' . \PHP_EOL
                    . '· `composure` - Make a composure roll (8)' . \PHP_EOL
                    . '· `judge` - Make a judge intentions check (5)' . \PHP_EOL
                    . '· `lift` - Make a lift/carry roll (6)' . \PHP_EOL
                    . '· `memory` - Make a memory test (6)' . \PHP_EOL
                    . '· `soak` - Make a soak test (1)' . \PHP_EOL
                    . '· `luck` - Make a luck (edge) test (6)' . \PHP_EOL
                    . '· `init` - Roll your initiative (1d6+7)' . \PHP_EOL
                    . '· `push 6 [limit] [text]` - Push the limit with 6 + your edge (6)' . \PHP_EOL
                    . '· `blitz` - Blitz initiative (5d6+7)' . \PHP_EOL,
                (string)$character,
            ),
            $response->attachments[1]->text
        );
        self::assertSame('Technomancer', $response->attachments[2]->title);
        self::assertSame(
            '· `fade` - Make a test to resist fading (11)' . \PHP_EOL,
            $response->attachments[2]->text
        );
        $character->delete();
    }

    /**
     * Test getting help in a channel that is attached to a campaign and the
     * current user is the GM.
     * @test
     */
    public function testHelpWithCampaignWithChatUserGM(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => Channel::TYPE_SLACK,
        ]);

        $channel->user = 'U' . Str::random(10);
        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response);
        self::assertSame(
            'Gamemaster commands',
            $response->attachments[1]->title
        );
    }
}
