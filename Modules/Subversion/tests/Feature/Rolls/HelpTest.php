<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Channel;
use App\Models\Character;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Modules\Subversion\Rolls\Help;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;
use function sprintf;

#[Group('subversion')]
#[Medium]
final class HelpTest extends TestCase
{
    /**
     * Test getting help via Slack for a channel as an unregistered user.
     */
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $channel = Channel::factory()->make([
            'system' => 'subversion',
            'type' => ChannelType::Slack,
        ]);
        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();
        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            sprintf('%s - Subversion', config('app.name')),
            $response['attachments'][0]['title'],
        );
        self::assertSame(
            'Note for unregistered users:',
            $response['attachments'][1]['title'],
        );
    }

    #[Group('discord')]
    public function testHelpInDiscordWithCharacter(): void
    {
        $channel = Channel::factory()->create([
            'system' => 'subversion',
            'type' => ChannelType::Discord,
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create(['system' => 'subversion']);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            sprintf('Subversion commands (as %s)', (string)$character),
            $response
        );

        $character->delete();
    }

    #[Group('irc')]
    public function testHelpIrc(): void
    {
        $channel = Channel::factory()->make([
            'system' => 'subversion',
            'type' => ChannelType::Irc,
        ]);
        $response = (new Help('', 'username', $channel))->forIrc();
        self::assertStringContainsString(
            sprintf('%s - Subversion', config('app.name')),
            $response,
        );
    }
}
