<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Cyberpunkred;

use App\Events\InitiativeAdded;
use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use App\Models\Cyberpunkred\Character;
use App\Models\Slack\TextAttachment;
use App\Rolls\Cyberpunkred\Init;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

use const PHP_EOL;

/**
 * Tests for rolling initiative in Cyberpunk Red.
 * @group cyberpunkred
 * @medium
 */
final class InitTest extends TestCase
{
    use WithFaker;

    /**
     * Test rolling init with no ChatUser registered in Slack and no reflexes.
     * @group slack
     */
    public function testSlackRollInitNoChatUserNoArgs(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Rolling initiative without a linked character requires your '
                . 'reflexes, and optionally any modififers: `/roll init 8 -2` '
                . 'for a character with 8 REF and a wound modifier of -2'
        );
        (new Init('init', $channel->username, $channel))->forSlack();

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init with no ChatUser registered in Discord and no reflexes.
     * @group discord
     */
    public function testDiscordRollInitNoChatUserNoArgs(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        $expected = 'Rolling initiative without a linked character requires '
            . 'your reflexes, and optionally any modififers: `/roll init 8 -2` '
            . 'for a character with 8 REF and a wound modifier of -2';
        $response = (new Init('init', $channel->username, $channel))
            ->forDiscord();

        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init with no ChatUser registered in IRC and no reflexes.
     * @group irc
     */
    public function testIRCRollInitNoChatUserNoArgs(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        $expected = 'Rolling initiative without a linked character requires '
            . 'your reflexes, and optionally any modififers: `/roll init 8 -2` '
            . 'for a character with 8 REF and a wound modifier of -2';
        $response = (new Init('init', $channel->username, $channel))
            ->forIrc();

        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init with no ChatUser in Slack, but including reflexes.
     * @group slack
     */
    public function testSlackRollInitNoChatUser(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        $response = (new Init('init 5', $channel->username, $channel))
            ->forSlack();

        $expected = [
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => TextAttachment::COLOR_SUCCESS,
                    'text' => 'Rolled: 5 + 5 = 10',
                    'title' => sprintf(
                        'Initiative added for %s',
                        $channel->username
                    ),
                ],
            ],
        ];
        self::assertSame($expected, $response->original);

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init with no ChatUser in Discord, but including reflexes
     * and a modifier.
     * @group discord
     */
    public function testDiscordRollInitNoChatUser(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;

        $response = (new Init('init 8 2', $channel->username, $channel))
            ->forDiscord();

        $expected = sprintf('**Initiative added for %s**', $channel->username)
            . PHP_EOL . 'Rolled: 4 + 8 + 2 = 14';
        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init in Slack with a linked character, but no campaign.
     * @group slack
     */
    public function testSlackRollInitCharacterNoCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        $response = (new Init('init', $channel->username, $channel))
            ->forSlack();

        $expected = [
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => TextAttachment::COLOR_SUCCESS,
                    'text' => sprintf(
                        'Rolled: 5 + %d = %d',
                        $character->reflexes,
                        $character->reflexes + 5
                    ),
                    'title' => sprintf(
                        'Initiative added for %s',
                        (string)$character
                    ),
                ],
            ],
        ];
        self::assertSame($expected, $response->original);

        Event::assertNotDispatched(InitiativeAdded::class);

        $character->delete();
    }

    /**
     * Test rolling init in Discord with a linked character and including the
     * character's reflexes and a modifier.
     * @group discord
     */
    public function testDiscordRollInitCharacterNoCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        $response = (new Init('init 5 -2', $channel->username, $channel))
            ->forDiscord();

        $expected = sprintf('**Initiative added for %s**', (string)$character)
            . PHP_EOL
            . sprintf(
                'Rolled: 4 + %d - 2 = %d',
                $character->reflexes,
                $character->reflexes + 4 - 2
            );
        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);

        $character->delete();
    }

    /**
     * Test rolling initiative with a linked character and campaign.
     * @group discord
     */
    public function testRollInitiativeWithCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'cyberpunkred']);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        $response = (new Init('init 5 -2', $channel->username, $channel))
            ->forDiscord();

        $expected = sprintf('**Initiative added for %s**', (string)$character)
            . PHP_EOL
            . sprintf(
                'Rolled: 4 + %d - 2 = %d',
                $character->reflexes,
                $character->reflexes + 4 - 2
            );
        self::assertSame($expected, $response);

        Event::assertDispatched(InitiativeAdded::class);

        $character->delete();
    }

    /**
     * Test rolling initiative without things linked.
     * @group irc
     */
    public function testIRCRollInitiativeNoCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = $channel->username;

        $response = (new Init('init 5 -2', $channel->username, $channel))
            ->forIrc();

        $expected = 'Initiative added for ' . $channel->username . PHP_EOL
            . 'Rolled: 4 + 5 - 2 = 7';
        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);
    }
}
