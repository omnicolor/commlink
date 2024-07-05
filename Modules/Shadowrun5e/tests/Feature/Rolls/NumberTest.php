<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Events\DiscordMessageReceived;
use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Discord\Builders\MessageBuilder;
use Discord\Discord;
use Discord\Parts\Channel\Channel as DiscordChannel;
use Discord\Parts\Channel\Message;
use Discord\Parts\Guild\Guild;
use Discord\Parts\Interactions\Interaction;
use Discord\Parts\User\User;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Number;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function sprintf;

use const PHP_EOL;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class NumberTest extends TestCase
{
    #[Group('slack')]
    #[TestDox('Test trying to roll without a limit or description')]
    public function testRollNoLimitNoDescription(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('5', 'user', $channel);
        $response = (string)$response->forSlack();
        self::assertStringNotContainsString('limit', $response);
        self::assertStringNotContainsString('for', $response);
    }

    #[Group('slack')]
    #[TestDox('Test trying to roll with a limit')]
    public function testRollWithLimit(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('15 5', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString('Limit: 5', $response);
        self::assertStringNotContainsString('for', $response);
    }

    #[Group('slack')]
    #[TestDox('Test trying to roll with a description')]
    public function testRollWithDescription(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('5 description', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringNotContainsString('limit', $response);
        self::assertStringContainsString('for \\"description\\"', $response);
    }

    #[Group('slack')]
    #[TestDox('Test trying to roll with both a description and a limit')]
    public function testRollBoth(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('20 10 description', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString('Limit: 10', $response);
        self::assertStringContainsString('for \\"description\\"', $response);
    }

    #[Group('slack')]
    #[TestDox('Test trying to roll too many dice')]
    public function testRollTooMany(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('You can\'t roll more than 100 dice!');
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        (new Number('101', 'username', $channel))->forSlack();
    }

    #[Group('slack')]
    #[TestDox('Test the user rolling a critical glitch')]
    public function testCriticalGlitch(): void
    {
        DiceService::shouldReceive('rollOne')->times(3)->with(6)->andReturn(1);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('3', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(
            'username rolled a critical glitch on 3 dice!',
            $response
        );
    }

    #[Group('slack')]
    #[TestDox('Test the footer formatting a user getting successes')]
    public function testFooterSixes(): void
    {
        DiceService::shouldReceive('rollOne')->times(3)->with(6)->andReturn(6);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('3', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString('*6* *6* *6*', $response);
    }

    #[TestDox('Test the description when the roll hits the limit')]
    #[Group('slack')]
    public function testDescriptionHitLimit(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(5);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'shadowrun5e']);
        $response = new Number('6 3 shooting', 'username', $channel);
        $response = (string)$response->forSlack();
        self::assertStringContainsString(
            'Rolled 3 successes for \\"shooting\\", hit limit',
            $response
        );
    }

    #[Group('discord')]
    #[TestDox('Test formatting a roll for Discord')]
    public function testFormattedForDiscord(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(6);

        $expected = '**username rolled 1 die**' . PHP_EOL
            . 'Rolled 1 successes' . PHP_EOL
            . 'Rolls: 6, Probability: 33.3333%';
        $response = new Number('1', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    #[Group('discord')]
    #[TestDox('Test formatting a roll for Discord with a limit and description')]
    public function testFormattedForDiscordMaxedOut(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(6);

        $expected = '**username rolled 6 dice with a limit of 3**' . PHP_EOL
            . 'Rolled 3 successes, hit limit' . PHP_EOL
            . 'Rolls: 6 6 6 6 6 6, Limit: 3, Probability: 0.1372%';
        $response = new Number('6 3', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    #[Group('discord')]
    #[TestDox('Test rolling too many dice in Discord')]
    public function testFormattedForDiscordTooManyDice(): void
    {
        $response = new Number('101', 'Loftwyr', new Channel());
        self::assertSame(
            'You can\'t roll more than 100 dice!',
            $response->forDiscord()
        );
    }

    #[Group('irc')]
    #[TestDox('Test rolling in IRC')]
    public function testIrc(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(6);

        $expected = 'username rolled 6 dice with a limit of 3' . PHP_EOL
            . 'Rolled 3 successes, hit limit' . PHP_EOL
            . 'Rolls: 6 6 6 6 6 6, Limit: 3';
        $response = new Number('6 3', 'username', new Channel());
        self::assertSame($expected, $response->forIrc());
    }

    #[Group('irc')]
    #[TestDox('Test an error in IRC')]
    public function testIrcError(): void
    {
        $response = new Number('101', 'Loftwyr', new Channel());
        self::assertSame(
            'You can\'t roll more than 100 dice!',
            $response->forIrc(),
        );
    }

    #[Group('discord')]
    #[TestDox('Test rolling with too many initial spaces')]
    public function testTooManySpaces(): void
    {
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(6);

        $expected = '**username rolled 1 die**' . PHP_EOL
            . 'Rolled 1 successes' . PHP_EOL
            . 'Rolls: 6, Probability: 33.3333%';
        $response = new Number(' 1', 'username', new Channel());
        self::assertSame($expected, $response->forDiscord());
    }

    #[Group('discord')]
    #[TestDox('Test rolling a SR 5e number in Discord with a critical glitch,
    # so they shouldn\'t see the second chance button')]
    public function testSecondChanceButtonMissingOnCritGlitch(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(1);

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
            'created_by' => self::class . '::' . __FUNCTION__,
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
        $expected = sprintf(
            '**%s rolled a critical glitch on 6 dice!**',
            (string)$character
        ) . PHP_EOL
            . 'Rolled 6 ones with no successes!' . PHP_EOL
            . 'Rolls: 1 1 1 1 1 1, Limit: 3, Probability: 100.0000%';

        $response = (new Number('6 3', (string)$character, $channel, $event))
            ->forDiscord();

        self::assertSame($expected, $response);
    }

    #[Group('discord')]
    #[TestDox('Test rolling a SR 5E number in Discord with a character that
    # hasn\'t used any edge yet, so they should see the second chance button')]
    public function testSeeSecondChanceFullEdge(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(3);

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
            'created_by' => self::class . '::' . __FUNCTION__,
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
        $expected = sprintf('**%s rolled 6 dice with a limit of 3**', (string)$character) . PHP_EOL
            . 'Rolled 0 successes' . PHP_EOL
            . 'Rolls: 3 3 3 3 3 3, Limit: 3, Probability: 100.0000%';

        /** @var MessageBuilder */
        $response = (new Number('6 3', (string)$character, $channel, $event))
            ->forDiscord();
        $response = $response->jsonSerialize();

        self::assertSame($expected, $response['content']);
        self::assertNotEmpty($response['components']);
    }

    #[Group('discord')]
    #[TestDox('Test another user trying to second chance a roll')]
    public function testAnotherUserClickingSecondChance(): void
    {
        DiceService::shouldReceive('rollOne')->times(6)->with(6)->andReturn(3);

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
            'created_by' => self::class . '::' . __FUNCTION__,
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
        $expected = sprintf('**%s rolled 6 dice with a limit of 3**', (string)$character) . PHP_EOL
            . 'Rolled 0 successes' . PHP_EOL
            . 'Rolls: 3 3 3 3 3 3, Limit: 3';

        $roll = (new Number('6 3', (string)$character, $channel, $event));
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

    #[Group('discord')]
    #[TestDox('Test a user using second chance')]
    public function testSecondChance(): void
    {
        DiceService::shouldReceive('rollOne')
            ->times(11)
            ->with(6)
            ->andReturn(6, 3, 3, 3, 3, 3, 1, 5, 5, 5, 6);

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
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $channelMock = self::createMock(DiscordChannel::class);
        $channelMock->method('__get')
            ->willReturn(self::createStub(Guild::class));
        $user = self::createStub(User::class);
        $map = [
            ['author', $user],
            ['channel', $channelMock],
            ['content', '/roll foo'],
        ];
        $messageMock = self::createMock(Message::class);
        $messageMock->method('__get')->willReturnMap($map);

        $event = new DiscordMessageReceived(
            $messageMock,
            self::createStub(Discord::class)
        );
        $roll = (new Number('6 3', (string)$character, $channel, $event));
        $roll->forDiscord();

        $interactedMessageMock = self::createMock(Message::class);
        $interactedMessageMock->method('__get')->willReturn($messageMock);
        $interactedMessageMock->expects(self::once())->method('edit');

        $interactionMap = [
            ['message', $interactedMessageMock],
            ['user', $user],
        ];
        $interactionMock = $this->createMock(Interaction::class);
        $interactionMock->method('__get')->willReturnMap($interactionMap);

        $roll->secondChance($interactionMock);
    }
}
