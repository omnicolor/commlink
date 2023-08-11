<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Fade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling a fade test Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class FadeTest extends TestCase
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
     * Test trying to roll a fade test without a character linked in Slack.
     * @group slack
     * @test
     */
    public function testWithoutCharacterSlack(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You must have a character linked to make fade tests'
        );
        (new Fade('', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll a fade test without a character linked in Discord.
     * @group discord
     * @test
     */
    public function testWithoutCharacterDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);

        self::assertSame(
            'username, You must have a character linked to make fade tests',
            (new Fade('', 'username', $channel))->forDiscord()
        );
    }

    /**
     * Test trying to make a fade test without being a technomancer.
     * @group discord
     * @test
     */
    public function testFadeNotTechnomancer(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'willpower' => 4,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Fade('', 'username', $channel))->forDiscord();
        self::assertSame(
            \sprintf(
                '%s, Your character must have a resonance attribute to make '
                    . 'fading tests',
                $character,
            ),
            $response,
        );

        $character->delete();
    }

    /**
     * Test a fade test.
     * @group discord
     * @test
     */
    public function testFadeDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'resonance' => 6,
            'willpower' => 5,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->randomInt->expects(self::exactly(11))->willReturn(6);
        $response = (new Fade('', 'username', $channel))->forDiscord();
        self::assertSame(
            \sprintf(
                '**%s rolled 11 dice for a fading test**'
                    . \PHP_EOL . 'Rolled 11 successes' . \PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }

    /**
     * Test a fade test in Slack.
     * @group slack
     * @test
     */
    public function testFadeSlack(): void
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
            'resonance' => 4,
            'willpower' => 3,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->randomInt->expects(self::exactly(7))->willReturn(2);
        $response = json_decode(
            (string)(new Fade('', 'username', $channel))->forSlack()
        );
        $attachment = $response->attachments[0];
        self::assertSame('2 2 2 2 2 2 2', $attachment->footer);
        self::assertSame(
            \sprintf('%s rolled 7 dice for a fading test', $character),
            $attachment->title
        );
        self::assertSame('Rolled 0 successes', $attachment->text);

        $character->delete();
    }

    /**
     * Test a fade test.
     * @group irc
     * @test
     */
    public function testFadeIRC(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_IRC,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'resonance' => 6,
            'willpower' => 5,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $this->randomInt->expects(self::exactly(11))->willReturn(6);
        $response = (new Fade('', 'username', $channel))->forIrc();
        self::assertSame(
            \sprintf(
                '%s rolled 11 dice for a fading test' . \PHP_EOL
                    . 'Rolled 11 successes' . \PHP_EOL
                    . 'Rolls: 6 6 6 6 6 6 6 6 6 6 6',
                (string)$character
            ),
            $response
        );

        $character->delete();
    }
}
