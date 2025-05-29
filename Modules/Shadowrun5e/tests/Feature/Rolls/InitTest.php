<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\User;
use Facades\App\Services\DiceService;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Rolls\Init;
use Omnicolor\Slack\Attachment;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use const PHP_EOL;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class InitTest extends TestCase
{
    /**
     * Test attempting a GM command as an unregistered user in a channel with
     * a campaign.
     */
    #[Group('slack')]
    public function testGmCommandUnregistered(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"',
        );
        (new Init('init clear', 'username', $channel))->forSlack();
    }

    /**
     * Test attempting to clear initiative as a GM.
     */
    #[Group('slack')]
    public function testGmClearInitiativeWithCampaign(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()
            ->hasInitiatives(3)
            ->create([
                'gm' => $user->id,
                'system' => 'shadowrun5e',
            ]);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Init('init clear', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'footer' => '',
                'text' => 'The GM has cleared the initiative tracker.',
                'title' => 'Initiative cleared',
            ],
            $response['attachments'][0],
        );
        self::assertDatabaseMissing(
            'initiatives',
            ['campaign_id' => $campaign->id],
        );
    }

    /**
     * Test attempting to start initiative as a GM.
     */
    #[Group('slack')]
    public function testGmStartInitiative(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()
            ->hasInitiatives(3)
            ->create([
                'gm' => $user->id,
                'system' => 'shadowrun5e',
            ]);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Init('init start', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'footer' => '',
                'text' => 'Type `/roll init` if your character is linked, or '
                    . '`/roll init A+Bd6` where A is your initiative score and B '
                    . 'is the number of initiative dice your character gets.',
                'title' => 'Roll initiative!',
            ],
            $response['attachments'][0],
        );
        self::assertDatabaseMissing(
            'initiatives',
            ['campaign_id' => $campaign->id],
        );
    }

    /**
     * Test attempting something else as a GM.
     */
    #[Group('slack')]
    public function testGmInvalidCommand(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()
            ->create([
                'gm' => $user->id,
                'system' => 'shadowrun5e',
            ]);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'That doesn\'t appear to be a valid GM initiative command'
        );
        (new Init('init 12+3d6', 'username', $channel))->forSlack();
    }

    /**
     * Test attempting to clear initiative in a channel without a campaign.
     */
    #[Group('discord')]
    public function testGmClearInitiativeWithoutCampaign(): void
    {
        $user = User::factory()->create();

        $channel = Channel::factory()
            ->hasInitiatives(3)
            ->create(['system' => 'shadowrun5e']);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Init('init clear', 'user', $channel))->forDiscord();

        self::assertSame(
            '**Initiative cleared**' . PHP_EOL
                . 'The GM has cleared the initiative tracker.',
            $response
        );
        self::assertDatabaseMissing(
            'initiatives',
            ['channel_id' => $channel->id]
        );
    }

    /**
     * Test attempted to create initiative in a channel without a campaign.
     */
    #[Group('discord')]
    public function testGmStartInitiativeWithoutCampaign(): void
    {
        $user = User::factory()->create();

        $channel = Channel::factory()
            ->hasInitiatives(3)
            ->create(['system' => 'shadowrun5e']);

        ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Init('init start', 'user', $channel))->forDiscord();

        self::assertSame(
            '**Roll initiative!**' . PHP_EOL
                . 'Type `/roll init` if your character is linked, or '
                . '`/roll init A+Bd6` where A is your initiative score and B '
                . 'is the number of initiative dice your character gets.',
            $response
        );
        self::assertDatabaseMissing(
            'initiatives',
            ['channel_id' => $channel->id],
        );
    }

    /**
     * Test rolling initiative with a linked Shadowrun character.
     */
    #[Group('slack')]
    public function testRollInitiativeForCharacter(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([6]);

        $user = User::factory()->create();

        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $character = Character::factory()->create([
            'intuition' => 4,
            'reaction' => 5,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Init('init', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'footer' => 'Rolls: 6',
                'text' => '9 + 1d6 = 15',
                'title' => 'Rolling initiative for ' . $character->handle,
            ],
            $response['attachments'][0],
        );
        self::assertDatabaseHas(
            'initiatives',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $character->id,
                'initiative' => 15,
            ],
        );

        $character->delete();
    }

    /**
     * Test manually rolling initiative in a channel without a campaign and the
     * user has no linked Character.
     */
    #[Group('slack')]
    public function testRollInitiativeForUser(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([4, 4, 4]);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12+3d6', 'username', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_INFO,
                'footer' => 'Rolls: 4 4 4',
                'text' => '12 + 3d6 = 24',
                'title' => 'Rolling initiative for username',
            ],
            $response['attachments'][0],
        );
        self::assertDatabaseHas(
            'initiatives',
            [
                'campaign_id' => null,
                'channel_id' => $channel->id,
                'character_id' => null,
                'character_name' => 'username',
                'initiative' => 24,
            ],
        );
    }

    /**
     * Test trying to roll initiative with too many arguments.
     */
    #[Group('slack')]
    public function testRollInitiativeTooManyArguments(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"',
        );

        (new Init('init ☃ 3 <script>', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll with base and dice as separate numbers with
     * a non-numeric base initiative.
     */
    #[Group('slack')]
    public function testRollInitiativeInvalidBaseInit(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"',
        );

        (new Init('init ☃ 3', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll with base and dice as separate numbers using
     * a non-numeric dice argument.
     */
    #[Group('discord')]
    public function testRollInitiativeInvalidDice(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12 Iñtërnâtiônàlizætiøn', 'user', $channel))
            ->forDiscord();
        self::assertSame(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"',
            $response,
        );
    }

    /**
     * Test trying to roll with base and dice as separate numbers.
     */
    #[Group('discord')]
    public function testRollInitiativeWithBaseAndDice(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12 3', 'user', $channel))->forDiscord();
        self::assertSame(
            '**Rolling initiative for user**' . PHP_EOL
                . '12 + 3d6 = 12 + 6 + 6 + 6 = 30',
            $response
        );
    }

    /**
     * Test manually rolling initiative trying to use too many dice.
     */
    #[Group('slack')]
    public function testRollInitiativeTooManyDice(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You can\'t roll more than five initiative dice',
        );

        (new Init('init 12+9d6', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to use the wrong sized dice for initiative.
     */
    #[Group('discord')]
    public function testRollInitiativeWrongDiceSize(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12+9d12', 'user', $channel))->forDiscord();
        self::assertSame(
            'Only six-sided dice can be used for initiative',
            $response
        );
    }

    /**
     * Test rolling using dice notation with a non-numeric base initiative.
     */
    #[Group('discord')]
    public function testRollInitiativeDiceNotationInvalidBase(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init A+9d6', 'user', $channel))->forDiscord();
        self::assertSame(
            'Initiative score must be a number',
            $response,
        );
    }

    /**
     * Test rolling using dice notation with a non-numeric number of dice.
     */
    #[Group('slack')]
    public function testRollDiceNotationInvalidDice(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Slack,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"',
        );

        (new Init('init 12+Ad6', 'username', $channel))->forSlack();
    }

    /**
     * Test rolling using just their base initiative.
     */
    #[Group('discord')]
    public function testRollJustBaseInitiative(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([1]);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Discord,
            'system' => 'shadowrun5e',
        ]);

        self::assertSame(
            '**Rolling initiative for user**' . PHP_EOL
                . '12 + 1d6 = 12 + 1 = 13',
            (new Init('init 12', 'user', $channel))->forDiscord(),
        );
    }

    #[Group('irc')]
    public function testRollInitiativeInvalidBaseIrc(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Irc,
            'system' => 'shadowrun5e',
        ]);

        self::assertSame(
            'Initiative score must be a number',
            (new Init('init A+9d6', 'user', $channel))->forIrc(),
        );
    }

    #[Group('irc')]
    public function testRollInitiativeIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([1]);

        $channel = Channel::factory()->create([
            'type' => ChannelType::Irc,
            'system' => 'shadowrun5e',
        ]);

        self::assertSame(
            'Rolling initiative for user' . PHP_EOL
                . '12 + 1d6 = 12 + 1 = 13',
            (new Init('init 12', 'user', $channel))->forIrc(),
        );
    }

    #[Group('irc')]
    public function testClearInitiativeIrc(): void
    {
        $channel = Channel::factory()->create([
            'type' => ChannelType::Irc,
            'system' => 'shadowrun5e',
        ]);

        self::assertSame(
            'Initiative cleared' . PHP_EOL
                . 'The GM has cleared the initiative tracker.',
            (new Init('init clear', 'user', $channel))->forIrc(),
        );
    }
}
