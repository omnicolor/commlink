<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Modules\Blistercritters\Models\Character;
use Modules\Blistercritters\Rolls\Help;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;
use function sprintf;

#[Group('alien')]
#[Medium]
final class HelpTest extends TestCase
{
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $channel = new Channel([
            'system' => 'blistercritters',
            'type' => ChannelType::Slack,
        ]);
        $response = (new Help('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertArrayHasKey('text', $response['attachments'][0]);
        self::assertSame(
            config('app.name') . ' - Blister Critters RPG',
            $response['attachments'][0]['title'],
        );
        self::assertStringStartsWith(
            'I am a bot that lets you roll Blister Critters RPG dice.',
            $response['attachments'][0]['text'],
        );
    }

    #[Group('discord')]
    public function testHelpWithCharacter(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'blistercritters',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create();
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);
        $response = (new Help('', 'username', $channel))->forDiscord();
        self::assertStringContainsString(
            sprintf('You\'re playing %s in this channel', (string)$character),
            $response,
        );
    }

    #[Group('irc')]
    public function testHelpIrc(): void
    {
        $channel = new Channel([
            'system' => 'blistercritters',
            'type' => ChannelType::Irc,
        ]);
        $response = (new Help('', 'username', $channel))->forIrc();
        self::assertStringStartsWith(
            config('app.name') . ' - Blister Critters RPG',
            $response,
        );
    }
}
