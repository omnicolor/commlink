<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Transformers\Enums\Programming;
use Modules\Transformers\Models\Character;
use Modules\Transformers\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

#[Group('transformers')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    public function testCreateUnknownStep(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'endurance_robot' => 5,
            'owner' => $user->email->address,
        ]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get(route('transformers.create', 'unknown'))
            ->assertNotFound();

        $character->delete();
    }

    public function testCreateNewCharacter(): void
    {
        session(['transformers-partial' => 'old-character-id']);
        $user = User::factory()->create();
        self::assertCount(
            0,
            PartialCharacter::where('owner', $user->email->address)->get(),
        );
        self::actingAs($user)
            ->get(route('transformers.create', 'new'))
            ->assertRedirect(route('transformers.create', 'base'));
        self::assertNotSame('old-character-id', session('transformers-partial'));
        self::assertCount(
            1,
            PartialCharacter::where('owner', $user->email->address)->get(),
        );
    }

    public function testCreateWithoutPartialCharacter(): void
    {
        $user = User::factory()->create();
        self::assertNull(session('transformers-partial'));
        self::assertCount(
            0,
            PartialCharacter::where('owner', $user->email->address)->get(),
        );
        self::actingAs($user)
            ->get(route('transformers.create'))
            ->assertOk()
            ->assertSee('What do other transformers know you as?');
        self::assertNotNull(session('transformers-partial'));
        self::assertCount(
            1,
            PartialCharacter::where('owner', $user->email->address)->get(),
        );
    }

    public function testChooseCharacter(): void
    {
        $user = User::factory()->create();
        $character1 = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        $character2 = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        self::actingAs($user)
            ->get(route('transformers.create'))
            ->assertOk()
            ->assertSee('Choose character');

        $character1->delete();
        $character2->delete();
    }

    public function testCreateWithPartialCharacterInUrl(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        self::actingAs($user)
            ->get(route('transformers.create', $character->id))
            ->assertRedirect(route('transformers.create', 'base'));

        $character->delete();
    }

    public function testCreateWithPartialCharacterInSession(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get(route('transformers.create'))
            ->assertOk()
            ->assertSee('What do other transformers know you as?');

        $character->delete();
    }

    public function testStoreBase(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->post(
                route('transformers.create-base'),
                [
                    'allegiance' => 'Autobots',
                    'color_primary' => 'red',
                    'color_secondary' => 'black',
                    'name' => 'Reaper',
                    'quote' => 'Victory is in the buttocks',
                ],
            )
            ->assertRedirect(route('transformers.create', 'statistics'));

        $character->refresh();
        self::assertSame('Reaper', $character->name);
        $character->delete();
    }

    public function testCreateTryAltModeBeforeStats(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get(route('transformers.create', 'alt-mode'))
            ->assertRedirect(route('transformers.create', 'statistics'));

        $character->delete();
    }

    public function testCreateAltMode(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'endurance_robot' => 5,
            'owner' => $user->email->address,
        ]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get(route('transformers.create', 'alt-mode'))
            ->assertOk()
            ->assertSee('04. Alt.Mode');

        $character->delete();
    }

    public function testCreateFunction(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get(route('transformers.create', 'function'))
            ->assertOk()
            ->assertSee('03. Function');

        $character->delete();
    }

    public function testStoreFunction(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->post(
                route('transformers.create-programming'),
                [
                    'programming' => 'engineer',
                ],
            )
            ->assertRedirect(route('transformers.create', 'alt-mode'));

        $character->refresh();
        self::assertSame(Programming::Engineer, $character->programming);
        $character->delete();
    }

    public function testCreateStatistics(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get(route('transformers.create', 'statistics'))
            ->assertOk()
            ->assertSee('02. Statistics');

        $character->delete();
    }

    public function testSaveStatistics(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->post(
                route('transformers.create-statistics'),
                [
                    'courage_robot' => 1,
                    'endurance_robot' => 2,
                    'firepower_robot' => 3,
                    'intelligence_robot' => 4,
                    'rank_robot' => 5,
                    'skill_robot' => 6,
                    'speed_robot' => 7,
                    'strength_robot' => 8,
                ],
            )
            ->assertRedirect(route('transformers.create', 'function'));

        $character->refresh();
        self::assertSame(1, $character->courage_robot);
        self::assertSame(2, $character->endurance_robot);
        self::assertSame(3, $character->firepower_robot);
        self::assertSame(4, $character->intelligence_robot);
        self::assertSame(5, $character->rank_robot);
        self::assertSame(6, $character->skill_robot);
        self::assertSame(7, $character->speed_robot);
        self::assertSame(8, $character->strength_robot);
        $character->delete();
    }

    public function testSaveForLater(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email->address]);
        session(['transformers-partial' => $character->id]);
        self::actingAs($user)
            ->get(route('transformers.create', 'save-for-later'))
            ->assertRedirect(route('dashboard'));

        self::assertNull(session('transformers-partial'));
        $character->delete();
    }

    public function testIndex(): void
    {
        $user = User::factory()->create();

        $character1 = Character::factory()
            ->create(['owner' => $user->email->address]);
        $character2 = Character::factory()
            ->create(['owner' => $user->email->address]);

        self::actingAs($user)
            ->getJson(route('transformers.characters.index'))
            ->assertOk()
            ->assertJsonCount(2, 'data');

        $character1->delete();
        $character2->delete();
    }

    public function testShowCharacter(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        $campaign = Campaign::factory()->create(['system' => 'transformers']);
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'owner' => $user->email->address,
            'subgroups' => ['actionmaster'],
        ]);

        self::actingAs($user)
            ->getJson(route('transformers.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name)
            ->assertJsonPath('data.campaign_id', $campaign->id)
            ->assertJsonCount(1, 'data.subgroups');

        $character->delete();
    }

    public function testViewCharacter(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()
            ->create(['owner' => $user->email->address]);

        self::actingAs($user)
            ->get(route('transformers.character', $character))
            ->assertSee($user->email->address)
            ->assertSee(e($character->name), false);

        $character->delete();
    }
}
