<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Events\DiscordMessageReceived;
use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Shadowrun5e\Number;
use Discord\Discord;
use Discord\Parts\Channel\Channel as DiscordChannel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\User;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling dice in Shadowrun 5E.
 * @group discord
 * @group shadowrun
 * @group shadowrun5e
 * @group slack
 * @medium
 */
final class NumberTest extends TestCase
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
     * Test trying to roll without a limit or description.
     * @test
     */
    public function testRollNoLimitNoDescription(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        $response = new Number('5', 'user', $channel);
        $response = (string)$response->forSlack();
        self::assertStringNotContainsString('limit', $response);
        self::assertStringNotContainsString('for', $response);
    }

    /**
     * Test trying to roll with a limit.
     * @test
     */
    public function testRollWithLimit(): void
    {
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('15 5', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(', limit: 5', $response);
        self::assertStringNotContainsString('for', $response);
    }

    /**
     * Test trying to roll with a description.
     * @test
     */
    public function testRollWithDescription(): void
    {
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('5 description', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringNotContainsString('limit', $response);
        self::assertStringContainsString('for \\"description\\"', $response);
    }

    /**
     * Test trying to roll with both a description and a limit.
     * @test
     */
    public function testRollBoth(): void
    {
        $this->randomInt->expects(self::any())->willReturn(random_int(1, 6));
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('20 10 description', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString('limit: 10', $response);
        self::assertStringContainsString('for \\"description\\"', $response);
    }

    /**
     * Test trying to roll too many dice.
     * @test
     */
    public function testRollTooMany(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('You can\'t roll more than 100 dice!');
        $this->randomInt->expects(self::never());
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        (new Number('101', 'username', $channel))->forSlack();
    }

    /**
     * Test the user rolling a critical glitch.
     * @test
     */
    public function testCriticalGlitch(): void
    {
        $this->randomInt->expects(self::exactly(3))->willReturn(1);
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('3', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(
            'username rolled a critical glitch on 3 dice!',
            $response
        );
    }

    /**
     * Test the footer formatting a user getting successes.
     * @test
     */
    public function testFooterSixes(): void
    {
        $this->randomInt->expects(self::exactly(3))->willReturn(6);
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('3', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString('*6* *6* *6*', $response);
    }

    /**
     * Test the description when the roll hits the limit.
     * @test
     */
    public function testDescriptionHitLimit(): void
    {
        $this->randomInt->expects(self::exactly(6))->willReturn(5);
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('6 3 shooting', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(
            'Rolled 3 successes for \\"shooting\\", hit limit',
            $response
        );
    }

    /**
     * Test formatting a roll for Discord.
     * @test
     */
    public function testFormattedForDiscord(): void
    {
        $expected = '**username rolled 1 die**' . \PHP_EOL
            . 'Rolled 1 successes' . \PHP_EOL
            . 'Rolls: 6';
        $this->randomInt->expects(self::exactly(1))->willReturn(6);
        $response = new Number('1', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    /**
     * Test formatting a roll for Discord with a limit and description.
     * @test
     */
    public function testFormattedForDiscordMaxedOut(): void
    {
        $expected = '**username rolled 6 dice with a limit of 3**' . \PHP_EOL
            . 'Rolled 3 successes, hit limit' . \PHP_EOL
            . 'Rolls: 6 6 6 6 6 6, Limit: 3';
        $this->randomInt->expects(self::exactly(6))->willReturn(6);
        $response = new Number('6 3', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    /**
     * Test rolling too many dice in Discord.
     * @test
     */
    public function testFormattedForDiscordTooManyDice(): void
    {
        $this->randomInt->expects(self::never());
        $response = new Number('101', 'Loftwyr', new Channel());
        self::assertSame(
            'You can\'t roll more than 100 dice!',
            $response->forDiscord()
        );
    }

    /**
     * Test rolling with too many initial spaces.
     * @test
     */
    public function testTooManySpaces(): void
    {
        $expected = '**username rolled 1 die**' . \PHP_EOL
            . 'Rolled 1 successes' . \PHP_EOL
            . 'Rolls: 6';
        $this->randomInt->expects(self::exactly(1))->willReturn(6);
        $response = new Number(' 1', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    /**
     * Test rolling a SR 5e number in Discord with a critical glitch, so they
     * shouldn't see the second chance button.
     * @test
     */
    public function testSecondChanceButtonMissingOnCritGlitch(): void
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
            'edge' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $channelStub = self::createStub(DiscordChannel::class);
        $channelStub->method('__get')
            ->willReturn(self::createStub(Guild::class));
        $map = [
            ['author', self::createStub(User::class)],
            ['channel', $channelStub],
            ['content', '/roll foo'],
        ];
        $message = self::createStub(Message::class);
        $message->method('__get')->willReturnMap($map);

        $event = new DiscordMessageReceived(
            $message,
            self::createStub(Discord::class)
        );
        $expected = \sprintf(
            '**%s rolled a critical glitch on 6 dice!**',
            (string)$character
        ) . \PHP_EOL
            . 'Rolled 6 ones with no successes!' . \PHP_EOL
            . 'Rolls: 1 1 1 1 1 1, Limit: 3';
        $this->randomInt->expects(self::exactly(6))->willReturn(1);

        $response = (new Number('6 3', (string)$character, $channel, $event))
            ->forDiscord();

        self::assertSame($expected, $response);
    }

    /**
     * Test rolling a SR 5E number in Discord with a character that hasn't used
     * any edge yet, so they should see the second chance button.
     * @test
     */
    public function testSeeSecondChanceFullEdge(): void
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
            'edge' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $channelStub = self::createStub(DiscordChannel::class);
        $channelStub->method('__get')
            ->willReturn(self::createStub(Guild::class));
        $map = [
            ['author', self::createStub(User::class)],
            ['channel', $channelStub],
            ['content', '/roll foo'],
        ];
        $message = self::createStub(Message::class);
        $message->method('__get')->willReturnMap($map);

        $event = new DiscordMessageReceived(
            $message,
            self::createStub(Discord::class)
        );
        $expected = \sprintf('**%s rolled 6 dice with a limit of 3**', (string)$character) . \PHP_EOL
            . 'Rolled 0 successes' . \PHP_EOL
            . 'Rolls: 3 3 3 3 3 3, Limit: 3';
        $this->randomInt->expects(self::exactly(6))->willReturn(3);

        /** @var \Discord\Builders\MessageBuilder */
        $response = (new Number('6 3', (string)$character, $channel, $event))
            ->forDiscord();
        $response = $response->jsonSerialize();

        self::assertSame($expected, $response['content']);
        self::assertNotEmpty($response['components']);
    }

    /**
     * Test another user trying to second chance a roll.
     * @test
     */
    public function testAnotherUserClickingSecondChance(): void
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
            'edge' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $channelStub = self::createStub(DiscordChannel::class);
        $channelStub->method('__get')
            ->willReturn(self::createStub(Guild::class));
        $map = [
            ['author', self::createStub(User::class)],
            ['channel', $channelStub],
            ['content', '/roll foo'],
        ];
        $message = self::createStub(Message::class);
        $message->method('__get')->willReturnMap($map);

        $event = new DiscordMessageReceived(
            $message,
            self::createStub(Discord::class)
        );
        $expected = \sprintf('**%s rolled 6 dice with a limit of 3**', (string)$character) . \PHP_EOL
            . 'Rolled 0 successes' . \PHP_EOL
            . 'Rolls: 3 3 3 3 3 3, Limit: 3';
        $this->randomInt->expects(self::exactly(6))->willReturn(3);

        $roll = new Number('6 3', (string)$character, $channel, $event);
        $roll->forDiscord();

        $interactedMessage = self::createStub(Message::class);
        $interactedMessage->method('__get')->willReturn($message);
        $interaction = $this->createMock(Interaction::class);
        $interaction->method('__get')->willReturn($interactedMessage);
        $roll->secondChance($interaction);

        // The character shouldn't be charged any edge.
        $character->refresh();
        self::assertNull($character->edgeCurrent);
    }

    /**
     * Test a user using second chance.
     * @group current
     * @test
     */
    public function testSecondChance(): void
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
            'edge' => 2,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $channelStub = self::createStub(DiscordChannel::class);
        $channelStub->method('__get')
            ->willReturn(self::createStub(Guild::class));
        $user = self::createStub(User::class);
        $map = [
            ['author', $user],
            ['channel', $channelStub],
            ['content', '/roll foo'],
        ];
        $message = self::createStub(Message::class);
        $message->method('__get')->willReturnMap($map);

        $event = new DiscordMessageReceived(
            $message,
            self::createStub(Discord::class)
        );
        $this->randomInt->expects(self::exactly(11))
            ->willReturnOnConsecutiveCalls(6, 3, 3, 3, 3, 3, 1, 5, 5, 5, 6);
        $roll = new Number('6 3', (string)$character, $channel, $event);
        $roll->forDiscord();

        $interactedMessage = self::createStub(Message::class);
        $interactedMessage->method('__get')->willReturn($message);
        // @phpstan-ignore-next-line
        $interactedMessage->expects(self::once())->method('edit');

        $interactionMap = [
            ['message', $interactedMessage],
            ['user', $user],
        ];
        $interaction = $this->createMock(Interaction::class);
        $interaction->method('__get')->willReturnMap($interactionMap);

        $roll->secondChance($interaction);
    }
}
