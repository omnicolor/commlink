<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Features\ChummerImport;
use App\Models\Campaign;
use App\Models\Character;
use App\Models\ChatUser;
use App\Models\Event;
use App\Models\User;
use App\ValueObjects\Email;
use InvalidArgumentException;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('campaigns')]
#[Group('user')]
#[Medium]
final class UserTest extends TestCase
{
    public function testEmailCast(): void
    {
        $user = new User(['email' => 'bob@example.com']);
        self::assertInstanceOf(Email::class, $user->email);
        self::assertSame('bob@example.com', $user->email->address);
    }

    public function testEmailInvalid(): void
    {
        $user = new User(['email' => 'bob']);
        self::expectException(InvalidArgumentException::class);
        // @phpstan-ignore expr.resultUnused
        $user->email;
    }

    public function testSetEmailInvalid(): void
    {
        $user = new User();
        $user->email = new Email('bob@example.com');
        self::assertSame('bob@example.com', $user->email->address);
    }

    /**
     * Test getting a user's campaigns if they have none.
     */
    public function testCampaignsNone(): void
    {
        $user = User::factory()->create();
        self::assertCount(0, $user->campaigns);
        self::assertCount(0, $user->campaignsRegistered);
    }

    /**
     * Test getting a user's campaigns.
     */
    public function testCampaignsGmed(): void
    {
        $user = User::factory()->create();
        Campaign::factory()->create(['gm' => $user]);
        Campaign::factory()->create([
            'gm' => $user,
            'registered_by' => $user,
        ]);
        self::assertCount(2, $user->campaignsGmed);
        self::assertCount(1, $user->campaignsRegistered);
    }

    /**
     * Test getting a user's characters if they have none.
     */
    public function testGetCharactersNone(): void
    {
        $user = User::factory()->create();
        self::assertEmpty($user->characters()->get());
    }

    /**
     * Test getting a user's characters if they have some.
     */
    public function testGetCharacters(): void
    {
        $user = User::factory()->create();
        $character1 = Character::factory()->create([
            'owner' => $user->email->address,
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email->address,
        ]);

        // @phpstan-ignore staticMethod.dynamicCall
        self::assertSame(2, $user->characters()->count());

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test getting a user's characters from a particular system.
     */
    public function testGetSystemCharacters(): void
    {
        $user = User::factory()->create();
        $character1 = Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'shadowrun5e',
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'cyberpunk2077',
        ]);
        // @phpstan-ignore staticMethod.dynamicCall
        self::assertSame(1, $user->characters('shadowrun5e')->count());

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test getting a character's ChatUsers if they have none.
     */
    public function testGetChatUsersNone(): void
    {
        $user = User::factory()->create();
        self::assertEmpty($user->chatUsers);
    }

    /**
     * Test getting a character's ChatUsers.
     */
    public function testGetChatUsers(): void
    {
        $user = User::factory()->create();
        ChatUser::factory()->create(['user_id' => $user->id]);
        self::assertNotEmpty($user->chatUsers);
    }

    #[Group('events')]
    public function testEventsEmpty(): void
    {
        $user = User::factory()->create();
        self::assertCount(0, $user->events);
    }

    #[Group('events')]
    public function testEvents(): void
    {
        $campaign = Campaign::factory()->create();
        $user = User::factory()->create();
        Event::create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'name' => 'User test event',
            'real_start' => now(),
        ]);
        self::assertCount(1, $user->events);
    }

    public function testGetFeatures(): void
    {
        $user = User::factory()->create();
        self::assertCount(0, $user->getFeatures());

        Feature::for($user)->activate(ChummerImport::class);
        self::assertCount(1, $user->getFeatures());
    }
}
