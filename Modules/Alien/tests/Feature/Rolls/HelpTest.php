<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rolls;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Modules\Alien\Models\Character;
use Modules\Alien\Rolls\Help;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function config;
use function json_decode;
use function sprintf;

#[Group('alien')]
#[Medium]
final class HelpTest extends TestCase
{
    #[Group('slack')]
    public function testHelpSlack(): void
    {
        $channel = new Channel([
            'system' => 'alien',
            'type' => Channel::TYPE_SLACK,
        ]);
        $response = (new Help('', 'username', $channel))->forSlack();
        $response = json_decode((string)$response);
        self::assertSame(
            config('app.name') . ' - Alien RPG',
            $response->attachments[0]->title
        );
        self::assertStringStartsWith(
            'I am a bot that lets you roll Alien RPG dice.',
            $response->attachments[0]->text,
        );
    }

    #[Group('discord')]
    public function testHelpWithCharacter(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
            'system' => 'alien',
        ]);
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);
        /** @var Character */
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
            'system' => 'alien',
            'type' => Channel::TYPE_IRC,
        ]);
        $response = (new Help('', 'username', $channel))->forIrc();
        self::assertStringStartsWith(
            config('app.name') . ' - Alien RPG',
            $response,
        );
    }
}
