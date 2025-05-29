<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Luck;
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
final class LuckTest extends TestCase
{
    /**
     * Test trying to roll a luck test without a character linked in Slack.
     */
    #[Group('slack')]
    public function testWithoutCharacterSlack(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make luck tests',
        );
        (new Luck('', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a luck test without a character linked in Discord.
     */
    #[Group('discord')]
    public function testWithoutCharacterDiscord(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make luck tests',
            (new Luck('', 'username', $channel))->forDiscord()
        );
    }

    #[Group('irc')]
    public function testWithoutCharacterIrc(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make luck tests',
            (new Luck('', 'username', $channel))->forIrc()
        );
    }

    /**
     * Test a character doing with would be a critical glitch on a luck test.
     */
    #[Group('slack')]
    public function testCritGlitch(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(3)
            ->with(6)
            ->andReturn(1);

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

        $character = Character::factory()->create(['edge' => 3]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Luck('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_DANGER,
                'footer' => '~1~ ~1~ ~1~',
                'text' => 'Rolled 0 successes',
                'title' => sprintf('%s rolled 3 dice for a luck test', $character),
            ],
            $response['attachments'][0],
        );

        $character->delete();
    }

    #[Group('discord')]
    public function testLuck(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(7)
            ->with(6)
            ->andReturn(6);

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

        $character = Character::factory()->create(['edge' => 7]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Luck('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '**%s rolled 7 dice for a luck test**'
                    . PHP_EOL . 'Rolled 7 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6, Probability: 0.0457%%',
                (string)$character
            ),
            $response
        );
        $character->delete();
    }

    #[Group('irc')]
    public function testLuckIrc(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(7)
            ->with(6)
            ->andReturn(6);

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

        $character = Character::factory()->create(['edge' => 7]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Luck('', 'username', $channel))->forIrc();
        self::assertSame(
            sprintf(
                '%s rolled 7 dice for a luck test'
                    . PHP_EOL . 'Rolled 7 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );
        $character->delete();
    }
}
