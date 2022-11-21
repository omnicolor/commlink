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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Event;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * Tests for rolling initiative in Cyberpunk Red.
 * @group cyberpunkred
 * @group discord
 * @group slack
 * @medium
 */
final class InitTest extends TestCase
{
    use PHPMock;
    use RefreshDatabase;
    use WithFaker;

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
            'App\\Rolls\\Cyberpunkred',
            'random_int'
        );
    }

    /**
     * Test rolling init with no ChatUser registered in Slack and no reflexes.
     * @test
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
     * @test
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
     * Test rolling init with no ChatUser in Slack, but including reflexes.
     * @test
     */
    public function testSlackRollInitNoChatUser(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = $this->faker->name;

        $this->randomInt->expects(self::exactly(1))->willReturn(5);

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
     * @test
     */
    public function testDiscordRollInitNoChatUser(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->make([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;

        $this->randomInt->expects(self::exactly(1))->willReturn(4);

        $response = (new Init('init 8 2', $channel->username, $channel))
            ->forDiscord();

        $expected = sprintf('**Initiative added for %s**', $channel->username)
            . \PHP_EOL . 'Rolled: 4 + 8 + 2 = 14';
        self::assertSame($expected, $response);

        Event::assertNotDispatched(InitiativeAdded::class);
    }

    /**
     * Test rolling init in Slack with a linked character, but no campaign.
     * @test
     */
    public function testSlackRollInitCharacterNoCampaign(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_SLACK,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . \Str::random(10);

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

        $this->randomInt->expects(self::exactly(1))->willReturn(5);

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
     * @test
     */
    public function testDiscordRollInitCharacterNoCampaign(): void
    {
        Event::fake();

        /** @var Channel */
        $channel = Channel::factory()->create([
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . \Str::random(10);

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

        $this->randomInt->expects(self::exactly(1))->willReturn(4);

        $response = (new Init('init 5 -2', $channel->username, $channel))
            ->forDiscord();

        $expected = sprintf('**Initiative added for %s**', (string)$character)
            . \PHP_EOL
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
     * @test
     */
    public function testRollInitiativeWithCampaign(): void
    {
        Event::fake();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'system' => 'cyberpunkred',
        ]);

        /** @var Channel */
        $channel = Channel::factory()->create([
            'campaign_id' => $campaign,
            'system' => 'cyberpunkred',
            'type' => Channel::TYPE_DISCORD,
        ]);
        $channel->username = $this->faker->name;
        $channel->user = 'U' . \Str::random(10);

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

        $this->randomInt->expects(self::exactly(1))->willReturn(4);

        $response = (new Init('init 5 -2', $channel->username, $channel))
            ->forDiscord();

        $expected = sprintf('**Initiative added for %s**', (string)$character)
            . \PHP_EOL
            . sprintf(
                'Rolled: 4 + %d - 2 = %d',
                $character->reflexes,
                $character->reflexes + 4 - 2
            );
        self::assertSame($expected, $response);

        Event::assertDispatched(InitiativeAdded::class);

        $character->delete();
    }
}
