<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Memory;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling a memory test Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class MemoryTest extends TestCase
{
    use PHPMock;

    /**
     * Mock random_int function to take randomness out of testing.
     * @var MockObject
     */
    protected MockObject $randomInt;

    /**
     * Set up the mock random function each time.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\\Shadowrun5e',
            'random_int'
        );
    }

    /**
     * Test trying to roll a memory test without a character linked in
     * Slack.
     * @group slack
     * @test
     */
    public function testWithoutCharacterSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make memory tests'
        );
        (new Memory('15 5', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a memory test without a character linked in
     * Discord.
     * @group discord
     * @test
     */
    public function testWithoutCharacterDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make memory tests',
            (new Memory('', 'username', $channel))->forDiscord()
        );
    }

    /**
     * Test a character critical glitching on a memory test.
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
            'logic' => 4,
            'willpower' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->randomInt->expects(self::exactly(6))->willReturn(1);
        $response = (new Memory('', 'username', $channel))->forSlack();
        $response = \json_decode((string)$response)->attachments[0];
        self::assertSame(
            \sprintf(
                '%s critically glitched on a memory roll!',
                $character
            ),
            $response->title
        );
        self::assertSame('Rolled 6 ones with no successes!', $response->text);

        $character->delete();
    }

    /**
     * Test a non-glitch memory test.
     * @test
     */
    public function testMemory(): void
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
            'logic' => 5,
            'willpower' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->randomInt->expects(self::exactly(8))->willReturn(6);
        $response = (new Memory('', 'username', $channel))->forDiscord();
        self::assertSame(
            \sprintf(
                '**%s rolled 8 dice for a memory test**'
                    . \PHP_EOL . 'Rolled 8 successes' . \PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6, Probability: 0.0152%%',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }
}
