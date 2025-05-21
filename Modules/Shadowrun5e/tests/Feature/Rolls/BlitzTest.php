<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Blitz;
use Omnicolor\Slack\Attachment;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

use const PHP_EOL;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class BlitzTest extends TestCase
{
    /**
     * Test trying to blitz initiative as the GM.
     */
    #[Group('slack')]
    public function testGmBlitz(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()
            ->create([
                'gm' => $user->id,
                'system' => 'shadowrun5e',
            ]);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage('GMs can\'t blitz initiative');
        (new Blitz('blitz', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to blitz without a character to pay edge from.
     */
    #[Group('slack')]
    public function testBlitzWithoutCharacter(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to blitz initiative',
        );
        (new Blitz('blitz', 'user', $channel))->forSlack();
    }

    /**
     * Test trying to blitz with a character that's out of edge.
     */
    #[Group('discord')]
    public function testBlitzOutOfEdge(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'edgeCurrent' => 0,
            'edge' => 5,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Blitz('blitz', 'username', $channel))->forDiscord();

        self::assertSame('It looks like you\'re out of edge!', $response);

        $character->delete();
    }

    #[Group('irc')]
    public function testBlitzErrorIrc(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Irc,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'edgeCurrent' => 0,
            'edge' => 5,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Blitz('blitz', 'username', $channel))->forIrc();

        self::assertSame('It looks like you\'re out of edge!', $response);

        $character->delete();
    }

    #[Group('slack')]
    public function testBlitzSlack(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(5, 6)
            ->andReturn([6, 6, 6, 6, 6]);

        $user = User::factory()->create();

        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'edge' => 4,
            'intuition' => 4,
            'reaction' => 5,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Blitz('blitz', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'footer' => 'Rolls: 6 6 6 6 6',
                'text' => '9 + 5d6 = 39',
                'title' => sprintf('%s blitzed', (string)$character),
            ],
            $response['attachments'][0],
        );
        self::assertDatabaseHas(
            'initiatives',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $character->id,
                'initiative' => 39,
            ],
        );

        $character->refresh();
        self::assertSame(3, $character->edgeCurrent);
        $character->delete();
    }

    /**
     * Test trying to blitz from Discord.
     */
    #[Group('discord')]
    public function testBlitzDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(5, 6)
            ->andReturn([5, 5, 5, 5, 5]);

        $user = User::factory()->create();

        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'edge' => 4,
            'edgeCurrent' => 3,
            'intuition' => 3,
            'reaction' => 3,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Blitz('blitz', 'username', $channel))->forDiscord();

        $expected = '**' . $character->handle . ' blitzed**' . PHP_EOL
            . '6 + 5d6 = 6 + 5 + 5 + 5 + 5 + 5 = 31';
        self::assertSame($expected, $response);

        self::assertDatabaseHas(
            'initiatives',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $character->id,
                'initiative' => 31,
            ]
        );

        $character->refresh();
        self::assertSame(2, $character->edgeCurrent);
        $character->delete();
    }

    #[Group('irc')]
    public function testBlitzIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(5, 6)
            ->andReturn([5, 5, 5, 5, 5]);

        $user = User::factory()->create();

        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Irc,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'edge' => 4,
            'edgeCurrent' => 3,
            'intuition' => 3,
            'reaction' => 3,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Blitz('blitz', 'username', $channel))->forIrc();
        $expected = $character->handle . ' blitzed' . PHP_EOL
            . '6 + 5d6 = 6 + 5 + 5 + 5 + 5 + 5 = 31';
        self::assertSame($expected, $response);

        self::assertDatabaseHas(
            'initiatives',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $character->id,
                'initiative' => 31,
            ]
        );

        $character->refresh();
        self::assertSame(2, $character->edgeCurrent);
        $character->delete();
    }
}
