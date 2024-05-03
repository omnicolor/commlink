<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Shadowrun5e\Character;
use App\Models\User;
use App\Rolls\Shadowrun5e\Init;
use Facades\App\Services\DiceService;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

/**
 * Tests for rolling initiative for Shadowrun 5th edition.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class InitTest extends TestCase
{
    /**
     * Test attempting a GM command as an unregistered user in a channel with
     * a campaign.
     * @group slack
     * @test
     */
    public function testGmCommandUnregistered(): void
    {
        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'campaign_id' => $campaign,
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"'
        );
        (new Init('init clear', 'username', $channel))->forSlack();
    }

    /**
     * Test attempting to clear initiative as a GM.
     * @group slack
     * @test
     */
    public function testGmClearInitiativeWithCampaign(): void
    {
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasInitiatives(3)
            ->create([
                'gm' => $user->id,
                'system' => 'shadowrun5e',
            ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Init('init clear', 'username', $channel))->forSlack();
        $response = json_decode((string)$response)->attachments[0];

        self::assertSame('Initiative cleared', $response->title);
        self::assertSame(
            'The GM has cleared the initiative tracker.',
            $response->text
        );
        self::assertDatabaseMissing(
            'initiatives',
            ['campaign_id' => $campaign->id]
        );
    }

    /**
     * Test attempting to start initiative as a GM.
     * @group slack
     * @test
     */
    public function testGmStartInitiative(): void
    {
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->hasInitiatives(3)
            ->create([
                'gm' => $user->id,
                'system' => 'shadowrun5e',
            ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        $response = (new Init('init start', 'username', $channel))->forSlack();
        $response = json_decode((string)$response)->attachments[0];

        self::assertSame('Roll initiative!', $response->title);
        self::assertSame(
            'Type `/roll init` if your character is linked, or '
                . '`/roll init A+Bd6` where A is your initiative score and B '
                . 'is the number of initiative dice your character gets.',
            $response->text
        );
        self::assertDatabaseMissing(
            'initiatives',
            ['campaign_id' => $campaign->id]
        );
    }

    /**
     * Test attempting something else as a GM.
     * @test
     */
    public function testGmInvalidCommand(): void
    {
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()
            ->create([
                'gm' => $user->id,
                'system' => 'shadowrun5e',
            ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
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
     * @test
     */
    public function testGmClearInitiativeWithoutCampaign(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()
            ->hasInitiatives(3)
            ->create(['system' => 'shadowrun5e']);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
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
     * @test
     */
    public function testGmStartInitiativeWithoutCampaign(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()
            ->hasInitiatives(3)
            ->create(['system' => 'shadowrun5e']);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
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
            ['channel_id' => $channel->id]
        );
    }

    /**
     * Test rolling initiative with a linked Shadowrun character.
     * @group slack
     * @test
     */
    public function testRollInitiativeForCharacter(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([6]);

        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'user_id' => $user->id,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'intuition' => 4,
            'reaction' => 5,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel->id,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser->id,
        ]);

        $response = (new Init('init', 'username', $channel))->forSlack();
        $response = json_decode((string)$response)->attachments[0];

        self::assertSame(
            'Rolling initiative for ' . $character->handle,
            $response->title
        );
        self::assertSame('9 + 1d6 = 15', $response->text);
        self::assertSame('Rolls: 6', $response->footer);
        self::assertDatabaseHas(
            'initiatives',
            [
                'campaign_id' => $campaign->id,
                'character_id' => $character->id,
                'initiative' => 15,
            ]
        );

        $character->delete();
    }

    /**
     * Test manually rolling initiative in a channel without a campaign and the
     * user has no linked Character.
     * @group slack
     * @test
     */
    public function testRollInitiativeForUser(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([4, 4, 4]);

        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12+3d6', 'username', $channel))->forSlack();
        $response = json_decode((string)$response)->attachments[0];

        self::assertSame(
            'Rolling initiative for username',
            $response->title
        );
        self::assertSame('12 + 3d6 = 24', $response->text);
        self::assertSame('Rolls: 4 4 4', $response->footer);
        self::assertDatabaseHas(
            'initiatives',
            [
                'campaign_id' => null,
                'channel_id' => $channel->id,
                'character_id' => null,
                'character_name' => 'username',
                'initiative' => 24,
            ]
        );
    }

    /**
     * Test trying to roll initiative with too many arguments.
     * @test
     */
    public function testRollInitiativeTooManyArguments(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"'
        );

        (new Init('init ☃ 3 <script>', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll with base and dice as separate numbers with
     * a non-numeric base initiative.
     * @test
     */
    public function testRollInitiativeInvalidBaseInit(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"'
        );

        (new Init('init ☃ 3', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to roll with base and dice as separate numbers using
     * a non-numeric dice argument.
     * @test
     */
    public function testRollInitiativeInvalidDice(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12 Iñtërnâtiônàlizætiøn', 'user', $channel))
            ->forDiscord();
        self::assertSame(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"',
            $response
        );
    }

    /**
     * Test trying to roll with base and dice as separate numbers.
     * @test
     */
    public function testRollInitiativeWithBaseAndDice(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([6, 6, 6]);

        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
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
     * @test
     */
    public function testRollInitiativeTooManyDice(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'You can\'t roll more than five initiative dice'
        );

        (new Init('init 12+9d6', 'username', $channel))->forSlack();
    }

    /**
     * Test trying to use the wrong sized dice for initiative.
     * @test
     */
    public function testRollInitiativeWrongDiceSize(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
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
     * @test
     */
    public function testRollInitiativeDiceNotationInvalidBase(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init A+9d6', 'user', $channel))->forDiscord();
        self::assertSame(
            'Initiative score must be a number',
            $response
        );
    }

    /**
     * Test rolling using dice notation with a non-numeric number of dice.
     * @test
     */
    public function testRollDiceNotationInvalidDice(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_SLACK,
            'system' => 'shadowrun5e',
        ]);

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Initiative is rolled like "/roll init 12 2" or "/roll init 12+2d6"'
        );

        (new Init('init 12+Ad6', 'username', $channel))->forSlack();
    }

    /**
     * Test rolling using just their base initiative.
     * @group discord
     * @test
     */
    public function testRollJustBaseInitiative(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([1]);

        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_DISCORD,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12', 'user', $channel))->forDiscord();
        self::assertSame(
            '**Rolling initiative for user**' . PHP_EOL
                . '12 + 1d6 = 12 + 1 = 13',
            $response
        );
    }

    /**
     * @group irc
     */
    public function testRollInitiativeInvalidBaseIrc(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init A+9d6', 'user', $channel))->forIrc();
        self::assertSame(
            'Initiative score must be a number',
            $response
        );
    }

    /**
     * @group irc
     */
    public function testRollInitiativeIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(1, 6)
            ->andReturn([1]);

        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init 12', 'user', $channel))->forIrc();
        self::assertSame(
            'Rolling initiative for user' . PHP_EOL
                . '12 + 1d6 = 12 + 1 = 13',
            $response
        );
    }

    /**
     * @group irc
     */
    public function testClearInitiativeIrc(): void
    {
        $user = User::factory()->create();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'type' => Channel::TYPE_IRC,
            'system' => 'shadowrun5e',
        ]);

        $response = (new Init('init clear', 'user', $channel))->forIrc();
        self::assertSame(
            'Initiative cleared' . PHP_EOL
                . 'The GM has cleared the initiative tracker.',
            $response,
        );
    }
}
