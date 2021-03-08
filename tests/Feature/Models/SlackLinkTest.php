<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Models\Character;
use App\Models\SlackLink;
use App\Models\User;

/**
 * Tests for the SlackLink model.
 * @group slack
 */
final class SlackLinkTest extends \Tests\TestCase
{
    /**
     * Character we're testing with.
     * @var Character
     */
    protected Character $character;

    /**
     * Clean up.
     */
    public function tearDown(): void
    {
        if (isset($this->character)) {
            $this->character->delete();
            unset($this->character);
        }
        parent::tearDown();
    }

    /**
     * Test a SlackLink that has no attached character.
     * @test
     */
    public function testNoCharacter(): void
    {
        $slackLink = SlackLink::factory()->make();
        self::assertNull($slackLink->character());
    }

    /**
     * Test a SlackLink that has no attached user.
     * @test
     */
    public function testNoUser(): void
    {
        $slackLink = SlackLink::factory()->make(['user_id' => null]);
        self::assertNull($slackLink->user);
    }

    /**
     * Test a SlackLink that has an attached character.
     * @test
     */
    public function testWithCharacter(): void
    {
        $this->character = Character::factory()->create();
        $slackLink = SlackLink::factory([
            'character_id' => $this->character->id,
        ])->make();
        self::assertSame(
            $this->character->handle,
            $slackLink->character()->handle
        );
    }

    /**
     * Test a SlackLink that has an attached user.
     * @test
     */
    public function testWithUser(): void
    {
        $user = User::factory()->create();
        $slackLink = SlackLink::factory(['user_id' => $user->id])->make();
        self::assertSame($user->name, $slackLink->user->name);
    }
}
