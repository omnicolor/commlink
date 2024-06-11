<?php

declare(strict_types=1);

namespace Tests\Feature\Models;

use App\Features\ChummerImport;
use App\Models\Campaign;
use App\Models\Character;
use App\Models\ChatUser;
use App\Models\Event;
use App\Models\User;
use Laravel\Pennant\Feature;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('campaigns')]
#[Group('user')]
#[Medium]
final class UserTest extends TestCase
{
    /**
     * Test getting a user's campaigns if they have none.
     */
    public function testCampaignsNone(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::assertCount(0, $user->campaigns);
        self::assertCount(0, $user->campaignsRegistered);
    }

    /**
     * Test getting a user's campaigns.
     */
    public function testCampaignsGmed(): void
    {
        /** @var User */
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
        /** @var User */
        $user = User::factory()->create();
        self::assertEmpty($user->characters()->get());
    }

    /**
     * Test getting a user's characters if they have some.
     */
    public function testGetCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $character1 = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);

        // @phpstan-ignore-next-line
        self::assertSame(2, $user->characters()->count());

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test getting a user's characters from a particular system.
     */
    public function testGetSystemCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $character1 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunk2077',
            'created_by' => self::class . '::' . __FUNCTION__,
        ]);
        // @phpstan-ignore-next-line
        self::assertSame(1, $user->characters('shadowrun5e')->count());

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test getting a character's ChatUsers if they have none.
     */
    public function testGetChatUsersNone(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::assertEmpty($user->chatUsers);
    }

    /**
     * Test getting a character's ChatUsers.
     */
    public function testGetChatUsers(): void
    {
        /** @var User */
        $user = User::factory()->create();
        /** @var ChatUser */
        $chatUser = ChatUser::factory()->create(['user_id' => $user->id]);
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
        /** @var Campaign */
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
        /** @var User */
        $user = User::factory()->create();
        self::assertCount(0, $user->getFeatures());

        Feature::for($user)->activate(ChummerImport::class);
        self::assertCount(1, $user->getFeatures());
    }
}
