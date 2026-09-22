<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Battletech\Models\Character;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

#[Group('battletech')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    private User $user;

    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        if (isset($this->user)) {
            return;
        }

        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $this->user = User::factory()->create();
        $this->user->assignRole($trusted);
    }

    public function testIndex(): void
    {
        $character1 = Character::factory()->create([
            'owner' => $this->user->email,
        ]);
        $character2 = Character::factory()->create([
            'owner' => $this->user->email,
        ]);

        self::actingAs($this->user)
            ->getJson(route('battletech.characters.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $character1->delete();
        $character2->delete();
    }

    public function testShowCharacter(): void
    {
        $campaign = Campaign::factory()->create(['system' => 'battletech']);
        $character = Character::factory()->create([
            'campaign_id' => $campaign->id,
            'owner' => $this->user->email,
        ]);

        self::actingAs($this->user)
            ->getJson(route('battletech.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name)
            ->assertJsonPath('data.campaign_id', $campaign->id);

        $character->delete();
    }

    public function testViewCharacter(): void
    {
        $character = Character::factory()->create([
            'name' => 'Jonny Rotten',
            'owner' => $this->user->email,
        ]);

        self::actingAs($this->user)
            ->get(route('battletech.character', $character))
            ->assertSee('Jonny Rotten');
        $character->delete();
    }
}
