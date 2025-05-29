<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Rolls;

use App\Enums\ChannelType;
use App\Events\InitiativeAdded;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\ChatCharacter;
use App\Models\ChatUser;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Modules\Cyberpunkred\Models\Character;
use Modules\Cyberpunkred\Rolls\Init;
use Omnicolor\Slack\Attachment;
use Omnicolor\Slack\Exceptions\SlackException;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

use const PHP_EOL;

#[Group('cyberpunkred')]
#[Medium]
final class InitTest extends TestCase
{
    use WithFaker;

    /**
     * Test rolling init with no ChatUser registered in Slack and no reflexes.
     */
    #[Group('slack')]
    public function testSlackRollInitNoChatUserNoArgs(): void
    {
        Event::fake();

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        self::expectException(SlackException::class);
        self::expectExceptionMessage(
            'Rolling initiative without a linked character requires your '
                . 'reflexes, and optionally any modififers: `/roll init 8 -2` '
                . 'for a character with 8 REF and a wound modifier of -2',
        );
        (new Init('init', $channel->username, $channel))->forSlack();

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init with no ChatUser registered in Discord and no reflexes.
     */
    #[Group('discord')]
    public function testDiscordRollInitNoChatUserNoArgs(): void
    {
        Event::fake();

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
     */
    #[Group('irc')]
    public function testIRCRollInitNoChatUserNoArgs(): void
    {
        Event::fake();

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
     */
    #[Group('slack')]
    public function testSlackRollInitNoChatUser(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        $response = (new Init('init 5', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();

        $expected = [
            'blocks' => [],
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => Attachment::COLOR_SUCCESS,
                    'text' => 'Rolled: 5 + 5 = 10',
                    'title' => sprintf(
                        'Initiative added for %s',
                        $channel->username
                    ),
                ],
            ],
        ];
        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init with no ChatUser in Discord, but including reflexes
     * and a modifier.
     */
    #[Group('discord')]
    public function testDiscordRollInitNoChatUser(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        $channel = Channel::factory()->make([
            'system' => 'cyberpunkred',
            'type' => ChannelType::Discord,
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
     */
    #[Group('slack')]
    public function testSlackRollInitCharacterNoCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(5);

        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => ChannelType::Slack,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_SLACK,
            'verified' => true,
        ]);

        $character = Character::factory()->create([]);

        ChatCharacter::factory()->create([
            'channel_id' => $channel,
            'character_id' => $character->id,
            'chat_user_id' => $chatUser,
        ]);

        $response = (new Init('init', $channel->username, $channel))
            ->forSlack()
            ->jsonSerialize();

        $expected = [
            'blocks' => [],
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => Attachment::COLOR_SUCCESS,
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
        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);

        $character->delete();
    }

    /**
     * Test rolling init in Discord with a linked character and including the
     * character's reflexes and a modifier.
     */
    #[Group('discord')]
    public function testDiscordRollInitCharacterNoCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => ChannelType::Discord,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create([]);

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
     */
    #[Group('discord')]
    public function testRollInitiativeWithCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        $campaign = Campaign::factory()->create(['system' => 'cyberpunkred']);

        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
            'type' => ChannelType::Discord,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . Str::random(10);

        $chatUser = ChatUser::factory()->create([
            'remote_user_id' => $channel->user,
            'server_id' => $channel->server_id,
            'server_type' => ChatUser::TYPE_DISCORD,
            'verified' => true,
        ]);

        $character = Character::factory()->create([]);

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
     */
    #[Group('irc')]
    public function testIRCRollInitiativeNoCampaign(): void
    {
        Event::fake();
        DiceService::shouldReceive('rollOne')->once()->with(10)->andReturn(4);

        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => ChannelType::Discord,
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
