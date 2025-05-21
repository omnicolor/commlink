<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Composure;
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
final class ComposureTest extends TestCase
{
    /**
     * Test trying to roll a composure test without a character linked in Slack.
     */
    #[Group('slack')]
    public function testWithoutCharacterSlack(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make Composure tests',
        );
        (new Composure('15 5', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a composure test without a character linked in
     * Discord.
     */
    #[Group('discord')]
    public function testWithoutCharacterDiscord(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make Composure tests',
            (new Composure('', 'username', $channel))->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testWithoutCharacterIrc(): void
    {
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make Composure tests',
            (new Composure('', 'username', $channel))->forIrc(),
        );
    }

    /**
     * Test a character critical glitching on a Composure test.
     */
    #[Group('slack')]
    public function testCritGlitch(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(6)
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

        $character = Character::factory()->create([
            'charisma' => 4,
            'willpower' => 2,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Composure('', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_DANGER,
                'footer' => '~1~ ~1~ ~1~ ~1~ ~1~ ~1~, Probability: 100.0000%',
                'text' => 'Rolled 6 ones with no successes!',
                'title' => sprintf(
                    '%s critically glitched on a composure roll!',
                    $character,
                ),
            ],
            $response['attachments'][0],
        );

        $character->delete();
    }

    /**
     * Test a non-glitch composure test.
     */
    #[Group('discord')]
    public function testComposure(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'charisma' => 5,
            'willpower' => 3,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        DiceService::shouldReceive('rollOne')
            ->times(8)
            ->with(6)
            ->andReturn(6);
        $response = (new Composure('', 'username', $channel))->forDiscord();
        self::assertSame(
            sprintf(
                '**%s rolled 8 dice for a composure test**'
                    . PHP_EOL . 'Rolled 8 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6, Probability: 0.0152%%',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }

    /**
     * Test a non-glitch composure test.
     */
    #[Group('irc')]
    public function testComposureIRC(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(8)
            ->with(6)
            ->andReturn(6);

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
            'charisma' => 5,
            'willpower' => 3,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Composure('', 'username', $channel))->forIrc();
        self::assertSame(
            sprintf(
                '%s rolled 8 dice for a composure test'
                    . PHP_EOL . 'Rolled 8 successes' . PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }
}
