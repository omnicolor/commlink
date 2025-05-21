<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Support\Str;
use Modules\Shadowrun5e\Rolls\Help;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

use const PHP_EOL;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class HelpTest extends TestCase
{
    /**
     * Test getting help via Slack.
     */
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $channel = Channel::factory()->make([
            'system' => 'shadowrun5e',
            'type' => ChannelType::Slack,
        ]);
        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'Commlink is a Slack/Discord bot that lets you roll '
                    . 'Shadowrun 5E dice.' . PHP_EOL
                    . '· `6 [text]` - Roll 6 dice, with optional text '
                    . '(automatics, perception, etc)' . PHP_EOL
                    . '· `12 6 [text]` - Roll 12 dice with a limit of 6'
                    . PHP_EOL
                    . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                    . 'optionally adding C to the result, optionally '
                    . 'describing that the roll is for "text"' . PHP_EOL,
                'title' => 'Commlink - Shadowrun 5th Edition',
            ],
            $response['attachments'][0],
        );
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'No character linked' . PHP_EOL
                    . '· `link <characterId>` - Link a character to this channel'
                    . PHP_EOL
                    . '· `init 12+3d6` - Roll your initiative' . PHP_EOL,
                'title' => 'Player',
            ],
            $response['attachments'][1],
        );
    }

    #[Group('discord')]
    public function testHelpDiscord(): void
    {
        $channel = Channel::factory()->make([
            'type' => ChannelType::Discord,
        ]);
        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            'Commlink - Shadowrun 5th Edition',
            $response,
        );
        self::assertStringContainsString(
            'No character linked',
            $response,
        );
    }

    /**
     * Test getting help in a channel that is attached to a campaign, but the
     * current user isn't registered.
     */
    #[Group('slack')]
    public function testHelpWithCampaignNoChatUser(): void
    {
        $campaign = Campaign::factory()->create([
            'system' => 'shadowrun5e',
        ]);
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Slack,
        ]);

        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'No character linked' . PHP_EOL
                    . '· `link <characterId>` - Link a character to this channel'
                    . PHP_EOL
                    . '· `init 12+3d6` - Roll your initiative' . PHP_EOL,
                'title' => 'Player',
            ],
            $response['attachments'][1],
        );
    }

    /**
     * Test getting help in a channel that is attached to a campaign, but the
     * current user, while linked, isn't the GM.
     */
    #[Group('slack')]
    public function testHelpWithCampaignWithChatUserNotGM(): void
    {
        $campaign = Campaign::factory()->create([
            'gm' => null,
            'system' => 'shadowrun5e',
        ]);
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
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
        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => 'No character linked' . PHP_EOL
                    . '· `link <characterId>` - Link a character to this channel'
                    . PHP_EOL
                    . '· `init 12+3d6` - Roll your initiative' . PHP_EOL,
                'title' => 'Player',
            ],
            $response['attachments'][1],
        );
    }

    /**
     * Test getting help in a channel that is attached to a campaign, but the
     * current user, while linked, isn't the GM.
     */
    #[Group('slack')]
    public function testHelpWithCampaignWithChatUserWithCharacter(): void
    {
        $campaign = Campaign::factory()->create([
            'gm' => null,
            'system' => 'shadowrun5e',
        ]);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'body' => 1,
            'charisma' => 2,
            'edge' => 6,
            'intuition' => 3,
            'system' => 'shadowrun5e',
            'reaction' => 4,
            'strength' => 5,
            'willpower' => 6,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => sprintf(
                    'You\'re playing %s in this channel' . PHP_EOL
                        . '· `composure` - Make a composure roll (8)' . PHP_EOL
                        . '· `judge` - Make a judge intentions check (5)' . PHP_EOL
                        . '· `lift` - Make a lift/carry roll (6)' . PHP_EOL
                        . '· `memory` - Make a memory test (6)' . PHP_EOL
                        . '· `soak` - Make a soak test (1)' . PHP_EOL
                        . '· `luck` - Make a luck (edge) test (6)' . PHP_EOL
                        . '· `init` - Roll your initiative (1d6+7)' . PHP_EOL
                        . '· `push 6 [limit] [text]` - Push the limit with 6 + your edge (6)' . PHP_EOL
                        . '· `blitz` - Blitz initiative (5d6+7)' . PHP_EOL,
                    (string)$character,
                ),
                'title' => 'Player',
            ],
            $response['attachments'][1],
        );

        $character->delete();
    }

    /**
     * Test getting help in a channel that is attached to a campaign and
     * a linked technomancer.
     */
    public function testHelpForTechnomancer(): void
    {
        $campaign = Campaign::factory()->create([
            'gm' => null,
            'system' => 'shadowrun5e',
        ]);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

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
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => sprintf(
                    'You\'re playing %s in this channel' . PHP_EOL
                        . '· `composure` - Make a composure roll (8)' . PHP_EOL
                        . '· `judge` - Make a judge intentions check (5)'
                        . PHP_EOL
                        . '· `lift` - Make a lift/carry roll (6)' . PHP_EOL
                        . '· `memory` - Make a memory test (6)' . PHP_EOL
                        . '· `soak` - Make a soak test (1)' . PHP_EOL
                        . '· `luck` - Make a luck (edge) test (6)' . PHP_EOL
                        . '· `init` - Roll your initiative (1d6+7)' . PHP_EOL
                        . '· `push 6 [limit] [text]` - Push the limit with 6 '
                        . '+ your edge (6)' . PHP_EOL
                        . '· `blitz` - Blitz initiative (5d6+7)' . PHP_EOL,
                    (string)$character,
                ),
                'title' => 'Player',
            ],
            $response['attachments'][1],
        );
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => '· `fade` - Make a test to resist fading (11)'
                    . PHP_EOL,
                'title' => 'Technomancer',
            ],
            $response['attachments'][2],
        );

        $character->delete();
    }

    /**
     * Test getting help in a channel that is attached to a campaign and the
     * current user is the GM.
     */
    #[Group('slack')]
    public function testHelpWithCampaignWithChatUserGM(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'system' => 'shadowrun5e',
            'type' => ChannelType::Slack,
        ]);
        $channel->user = 'U' . Str::random(10);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user,
            'verified' => true,
        ]);

        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'text' => '· `init start` - Start a new initiative tracker, '
                    . 'removing any existing rolls' . PHP_EOL,
                'title' => 'Gamemaster commands',
            ],
            $response['attachments'][1],
        );
    }
}
