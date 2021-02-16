<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Slack;

use App\Models\Character;
use App\Models\Slack\Channel;
use App\Models\SlackLink;
use App\Models\User;
use Str;

/**
 * Tests for Slack channels.
 * @covers \App\Models\Slack\Channel
 * @group models
 * @group slack
 */
final class ChannelTest extends \Tests\TestCase
{
    /**
     * Channel used for tests.
     * @var ?Channel
     */
    protected ?Channel $channel;

    /**
     * Character used for tests.
     * @var ?Character
     */
    protected ?Character $character;

    /**
     * Clean up after the tests.
     */
    public function tearDown(): void
    {
        if (isset($this->channel)) {
            $this->channel->delete();
            unset($this->channel);
        }
        if (isset($this->character)) {
            $this->character->delete();
            unset($this->character);
        }
        parent::tearDown();
    }

    /**
     * Test the accessor on an unregistered channel.
     * @test
     */
    public function testGettingSystemUnregistered(): void
    {
        $this->channel = Channel::factory()->create(['system' => null]);
        self::assertSame('unregistered', $this->channel->system);
    }

    /**
     * Test the accessor on a registered channel.
     * @test
     */
    public function testGettingSystemRegistered(): void
    {
        $this->channel = Channel::factory()
            ->create(['system' => 'cyberpunkred']);
        self::assertSame('cyberpunkred', $this->channel->system);
    }

    /**
     * Test getting the default username.
     * @test
     */
    public function testGetUsernameDefault(): void
    {
        $this->channel = Channel::factory()->create();
        self::assertSame('Unknown', $this->channel->username);
    }

    /**
     * Test setting and getting the channel's username.
     * @test
     */
    public function testSetUsername(): void
    {
        $this->channel = Channel::factory()->make();
        $this->channel->username = 'Test User';
        self::assertSame('Test User', $this->channel->username);
    }

    /**
     * Test the channel's username getting set with a character.
     * @test
     */
    public function testUsernameFromCharacter(): void
    {
        $user = User::factory()->create();
        $this->character = Character::create([
            'handle' => 'CharName',
            'system' => 'shadowrun5e',
        ]);
        $this->channel = Channel::factory()->create([
            'team' => Str::random(10),
        ]);
        $this->channel->user = \Str::random(10);
        $this->channel->username = 'Foo Bar';
        $link = SlackLink::create([
            'character_id' => $this->character->id,
            'slack_team' => $this->channel->team,
            'slack_user' => $this->channel->user,
            'user_id' => $user->id,
        ]);
        self::assertSame('CharName (Foo Bar)', $this->channel->username);
    }

    /**
     * Test the channel's username getting set with a character.
     * @test
     */
    public function testUsernameFromCharacterNoSlack(): void
    {
        $user = User::factory()->create();
        $this->character = Character::create([
            'name' => 'Crowes',
            'system' => 'shadowrun5e',
        ]);
        $this->channel = Channel::factory()->create([
            'team' => Str::random(10),
        ]);
        $this->channel->user = \Str::random(10);
        $link = SlackLink::create([
            'character_id' => $this->character->id,
            'slack_team' => $this->channel->team,
            'slack_user' => $this->channel->user,
            'user_id' => $user->id,
        ]);
        self::assertSame('Crowes', $this->channel->username);
    }
}
