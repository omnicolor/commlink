<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Shadowrun5e\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function sprintf;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class ContactsControllerTest extends TestCase
{
    /**
     * Test getting contacts for another user without being the GM.
     */
    public function testGetContactsNotRelated(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create();

        self::actingAs($user)
            ->get(sprintf('/api/shadowrun5e/characters/%s/contacts', $character->id))
            ->assertNotFound();
        $character->delete();
    }

    /**
     * Test getting contacts for a character owned by the current user.
     */
    public function testGetContactsAsOwner(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'contacts' => [
                [
                    'archetype' => 'Decker',
                    'connection' => 7,
                    'loyalty' => null,
                    'gmNotes' => 'Legendary',
                    'name' => 'Dodger',
                ],
            ],
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->get(sprintf('/api/shadowrun5e/characters/%s/contacts', $character->id))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Dodger'])
            ->assertJsonMissing(['gmNotes' => 'Legendary']);
        $character->delete();
    }

    /**
     * Test getting contacts for a character that plays in a campaign, but the
     * requesting user isn't the GM.
     */
    public function testGetContactsAsAnotherPlayer(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'contacts' => [
                [
                    'archetype' => 'Decker',
                    'connection' => 7,
                    'loyalty' => null,
                    'gmNotes' => 'Legendary',
                    'name' => 'Dodger',
                ],
            ],
        ]);

        self::actingAs($user)
            ->get(sprintf('/api/shadowrun5e/characters/%s/contacts', $character->id))
            ->assertNotFound();
        $character->delete();
    }

    /**
     * Test getting contacts for a character that plays in a campaign and the
     * requestor is the GM.
     */
    public function testGetContactsAsGm(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'contacts' => [
                [
                    'archetype' => 'Decker',
                    'connection' => 7,
                    'loyalty' => null,
                    'gmNotes' => 'Legendary',
                    'name' => 'Dodger',
                ],
            ],
        ]);

        self::actingAs($user)
            ->get(sprintf('/api/shadowrun5e/characters/%s/contacts', $character->id))
            ->assertOk()
            ->assertJsonFragment(['name' => 'Dodger'])
            ->assertJsonFragment(['gmNotes' => 'Legendary']);
        $character->delete();
    }

    /**
     * Test trying to create a contact for a Shadowrun 5E character that has no
     * campaign.
     */
    public function testCreateContactNoCampaign(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'contacts' => [],
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->postJson(
                sprintf('/api/shadowrun5e/characters/%s/contacts', $character->id),
                []
            )
            ->assertForbidden();
        $character->delete();
    }

    /**
     * Test trying to create a contact for a Shadowrun 5E character that has
     * a campaign, but the current user is not the GM.
     */
    public function testCreateContactNotGM(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'contacts' => [
                [
                    'archetype' => 'Decker',
                    'connection' => 7,
                    'loyalty' => null,
                    'gmNotes' => 'Legendary',
                    'name' => 'Dodger',
                ],
            ],
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->postJson(
                sprintf('/api/shadowrun5e/characters/%s/contacts', $character->id),
                []
            )
            ->assertForbidden();
        $character->delete();
    }

    /**
     * Test creating a contact for a Shadowrun 5E character as the GM.
     */
    public function testCreateContactAsGm(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'contacts' => [
                [
                    'archetype' => 'Decker',
                    'connection' => 7,
                    'loyalty' => null,
                    'gmNotes' => 'Legendary',
                    'name' => 'Dodger',
                ],
            ],
        ]);

        self::actingAs($user)
            ->postJson(
                sprintf('/api/shadowrun5e/characters/%s/contacts', $character->id),
                [
                    'archetype' => 'Fixer',
                    'name' => 'Bob Loblaw',
                ]
            )
            ->assertCreated();
        $character->delete();
    }
}
