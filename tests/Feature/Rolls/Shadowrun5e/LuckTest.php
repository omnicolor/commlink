<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Luck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling a luck test Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class LuckTest extends TestCase
{
    use PHPMock;
    use RefreshDatabase;

    protected MockObject $randomInt;

    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\\Shadowrun5e',
            'random_int'
        );
    }

    /**
     * Test trying to roll a luck test without a character linked in Slack.
     * @group slack
     * @test
     */
    public function testWithoutCharacterSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make luck tests'
        );
        (new Luck('', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a luck test without a character linked in Discord.
     * @group discord
     * @test
     */
    public function testWithoutCharacterDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make luck tests',
            (new Luck('', 'username', $channel))->forDiscord()
        );
    }

    /**
     * Test a character doing with would be a critical glitch on a luck test.
     * @test
     */
    public function testCritGlitch(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'edge' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->randomInt->expects(self::exactly(3))->willReturn(1);
        $response = (new Luck('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response)->attachments[0];
        self::assertSame(
            \sprintf('%s rolled 3 dice for a luck test', $character),
            $response->title
        );
        self::assertSame('Rolled 0 successes', $response->text);
        $character->delete();
    }

    /**
     * Test a non-glitch luck test.
     * @test
     */
    public function testLuck(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'edge' => 7,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->randomInt->expects(self::exactly(7))->willReturn(6);
        $response = (new Luck('', 'username', $channel))->forDiscord();
        self::assertSame(
            \sprintf(
                '**%s rolled 7 dice for a luck test**'
                    . \PHP_EOL . 'Rolled 7 successes' . \PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );
        $character->delete();
    }
}
