<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Fade;
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
final class FadeTest extends TestCase
{
    /**
     * Test trying to roll a fade test without a character linked in Slack.
     */
    #[Group('slack')]
    public function testWithoutCharacterSlack(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make fade tests'
        );
        (new Fade('', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a fade test without a character linked in Discord.
     */
    #[Group('discord')]
    public function testWithoutCharacterDiscord(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make fade tests',
            (new Fade('', 'username', $channel))->forDiscord()
        );
    }

    /**
     * Test trying to make a fade test without being a technomancer.
     */
    #[Group('discord')]
    public function testFadeNotTechnomancer(): void
    {
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);

        $character = Character::factory()->create(['willpower' => 4]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '%s, Your character must have a resonance attribute to make '
                    . 'fading tests',
                $character,
            ),
            $response,
        );

        $character->delete();
    }

    #[Group('discord')]
    public function testFadeDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->times(11)->with(6)->andReturn(6);

        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'resonance' => 6,
            'willpower' => 5,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '**%s rolled 11 dice for a fading test**'
                    . PHP_EOL . 'Rolled 11 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6 6 6 6, Probability: 0.0006%%',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }

    #[Group('slack')]
    public function testFadeSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(7)
            ->with(6)
            ->andReturn(2);

        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'resonance' => 4,
            'willpower' => 3,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_SUCCESS,
                'footer' => '2 2 2 2 2 2 2, Probability: 100.0000%',
                'text' => 'Rolled 0 successes',
                'title' => sprintf('%s rolled 7 dice for a fading test', $character),
            ],
            $response['attachments'][0],
        );

        $character->delete();
    }

    #[Group('irc')]
    public function testErrorIrc(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make fade tests',
            (new Fade('', 'username', $channel))->forIrc()
        );
    }

    #[Group('irc')]
    public function testFadeIRC(): void
    {
        DiceService::shouldReceive('rollOne')->times(11)->with(6)->andReturn(6);

        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'resonance' => 6,
            'willpower' => 5,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))->forIrc();
        self::assertSame(
            sprintf(
                '%s rolled 11 dice for a fading test' . PHP_EOL
                    . 'Rolled 11 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }
}
