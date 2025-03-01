<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Rolls;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\WebChannel;
use Facades\App\Services\DiceService;
use Modules\Alien\Models\Character;
use Modules\Alien\Rolls\Skill;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

use const PHP_EOL;

#[Group('alien')]
#[Medium]
final class SkillTest extends TestCase
{
    #[Group('slack')]
    public function testSkillWithoutCharacter(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Skill rolls are only available if you have linked a character',
        );

        (new Skill('skill close-combat', 'username', new Channel()))
            ->forSlack();
    }

    #[Group('irc')]
    public function testInvalidSkill(): void
    {
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'alien',
        ]);
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);
        $character = Character::factory()->create();
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Skill('skill invalid', 'user', $channel))->forIrc();
        self::assertSame('Skill "invalid" is not valid', $response);
    }

    #[Group('discord')]
    public function testSkillRoll(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(6, 1, 3, 3, 6, 1);

        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'alien',
        ]);
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);
        $character = Character::factory()->create([
            'skills' => [
                'close-combat' => 4,
            ],
            'strength' => 2,
        ]);
        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);
        $response = (new Skill('skill close-combat', 'user', $channel))
            ->forDiscord();
        $expected = sprintf(
            '**%s succeeded with 6 dice for Close combat (4+2+0)**' . PHP_EOL
                . 'Rolled 2 successes' . PHP_EOL
                . 'Rolls: 6 1 3 3 6 1',
            (string)$character,
        );
        self::assertSame($expected, $response);
    }

    public function testForWeb(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
            ->with(6)
            ->andReturn(6, 1, 3, 3, 6, 1);

        $character = Character::factory()->make([
            'skills' => [
                'close-combat' => 4,
            ],
            'strength' => 2,
        ]);
        $channel = new WebChannel();
        $channel->setCharacter($character);
        $response = (new Skill('skill close-combat', 'user', $channel))
            ->forWeb();
        $expected = [
            'panic' => false,
            'pushable' => true,
            'rolls' => [6, 1, 3, 3, 6, 1],
            'success' => true,
            'text' => 'Rolled 2 successes',
            'title' => sprintf(
                '%s succeeded with 6 dice for Close combat (4+2+0)',
                (string)$character,
            ),
        ];
        self::assertSame($expected, $response);
    }
}
