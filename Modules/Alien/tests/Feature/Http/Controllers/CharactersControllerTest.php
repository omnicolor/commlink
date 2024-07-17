<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Facades\App\Services\DiceService;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Alien\Http\Controllers\CharactersController;
use Modules\Alien\Models\Character;
use Modules\Alien\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

#[Group('alien')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    use WithFaker;

    public function testSaveForLater(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);

        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'save-for-later'))
            ->assertRedirect(route('dashboard'));
        self::assertNull(session(CharactersController::SESSION_KEY));
    }

    public function testCreateNewCharacter(): void
    {
        $user = User::factory()->create();
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(0, $characters);
        self::actingAs($user)
            ->get(route('alien.create', 'new'))
            ->assertRedirect(route('alien.create', 'career'));
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);

        // @phpstan-ignore-next-line
        $characters[0]->delete();
    }

    public function testImplicitlyCreateNew(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->get(route('alien.create'))
            ->assertOk()
            ->assertSee('Pick a name');
        PartialCharacter::where('owner', $user->email)->delete();
    }

    public function testCreateCharacterChoose(): void
    {
        $user = User::factory()->create();
        $character1 = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        $character2 = PartialCharacter::factory()
            ->create(['owner' => $user->email]);

        self::actingAs($user)
            ->get(route('alien.create'))
            ->assertOk()
            ->assertSee('Choose character');

        $character1->delete();
        $character2->delete();
    }

    public function testContinueCharacter(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->get(route('alien.create', $character->id))
            ->assertRedirect(route('alien.create', 'career'))
            ->assertSessionHas(CharactersController::SESSION_KEY, $character->id);
        $character->delete();
    }

    public function testCreateUnknown(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'unknown'))
            ->assertNotFound();
        $character->delete();
    }

    public function testCreateCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'career'))
            ->assertOk()
            ->assertSee('Pick a name');
        $character->delete();
    }

    public function testSaveCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        $name = $this->faker->name;
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-career'),
                [
                    'career' => 'colonial-marine',
                    'name' => $name,
                ],
            )
            ->assertRedirect(route('alien.create', 'attributes'));
        $character->refresh();
        self::assertSame('colonial-marine', $character->career->id);
        self::assertSame($name, $character->name);
        $character->delete();
    }

    public function testCreateAttributesWithCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'attributes'))
            ->assertOk()
            ->assertSee('When you create your player character')
            ->assertDontSee('be greater than 4');
        $character->delete();
    }

    public function testCreateAttributesNoCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'attributes'))
            ->assertOk()
            ->assertSee('When you create your player character')
            ->assertSee('be greater than 4');
        $character->delete();
    }

    public function testSaveAttributes(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-attributes'),
                [
                    'agility' => 2,
                    'empathy' => 2,
                    'strength' => 5,
                    'wits' => 4,
                ],
            )
            ->assertRedirect(route('alien.create', 'skills'));
        $character->refresh();
        self::assertSame(2, $character->agility);
        self::assertSame(2, $character->empathy);
        self::assertSame(5, $character->strength);
        self::assertSame(4, $character->wits);
        $character->delete();
    }

    public function testCreateSkillsNoCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'skills'))
            ->assertOk()
            ->assertSee('skills are the knowledge and abilities')
            ->assertSee('You haven\'t chosen a career', false);
        $character->delete();
    }

    public function testCreateSkillsWithCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'skills'))
            ->assertOk()
            ->assertSee('skills are the knowledge and abilities')
            ->assertDontSee('You haven\'t chosen a career', false);
        $character->delete();
    }

    public function testSaveSkills(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-skills'),
                [
                    'close-combat' => 3,
                    'command' => 1,
                    'comtech' => 0,
                    'heavy-machinery' => 0,
                    'manipulation' => 0,
                    'medical-aid' => 0,
                    'mobility' => 0,
                    'observation' => 0,
                    'piloting' => 0,
                    'ranged-combat' => 3,
                    'stamina' => 3,
                    'survival' => 0,
                ],
            )
            ->assertRedirect(route('alien.create', 'talent'));
        $character->refresh();
        self::assertSame(3, $character->skills['close-combat']->rank);
        self::assertSame(1, $character->skills['command']->rank);
        self::assertSame(0, $character->skills['comtech']->rank);
        self::assertSame(0, $character->skills['heavy-machinery']->rank);
        self::assertSame(0, $character->skills['manipulation']->rank);
        self::assertSame(0, $character->skills['medical-aid']->rank);
        self::assertSame(0, $character->skills['mobility']->rank);
        self::assertSame(0, $character->skills['observation']->rank);
        self::assertSame(0, $character->skills['piloting']->rank);
        self::assertSame(3, $character->skills['ranged-combat']->rank);
        self::assertSame(3, $character->skills['stamina']->rank);
        self::assertSame(0, $character->skills['survival']->rank);
        $character->delete();
    }

    public function testCreateTalentNoCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'talent'))
            ->assertRedirect(route('alien.create', 'career'))
            ->assertSessionHasErrors([
                'error' => 'You must choose a career before you can choose a talent',
            ]);
        $character->delete();
    }

    public function testCreateTalentWithCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
            'talents' => ['banter'],
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'talent'))
            ->assertOk()
            ->assertSee('Talents are tricks, moves, and minor abilities');
        $character->delete();
    }

    public function testSaveTalent(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-talent'),
                ['talent' => 'banter'],
            )
            ->assertRedirect(route('alien.create', 'gear'));
        $character->refresh();
        self::assertSame('banter', $character->talents[0]->id);
        $character->delete();
    }

    public function testCreateGearNoCareer(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()
            ->create(['owner' => $user->email]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'gear'))
            ->assertRedirect(route('alien.create', 'career'))
            ->assertSessionHasErrors([
                'error' => 'You must choose a career before you can select gear',
            ]);
        $character->delete();
    }

    public function testCreateGear(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'armor' => 'm3-personnel-armor',
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'gear'))
            ->assertOk()
            ->assertSee('To survive the world of ALIEN, you need the right gear');
        $character->delete();
    }

    public function testSaveGearWeaponAndArmor(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-gear'),
                [
                    'gear' => [
                        'armat-m41ae2-heavy-pulse-rifle',
                        'm3-personnel-armor',
                    ],
                ],
            )
            ->assertRedirect(route('alien.create', 'finish'));
        $character->refresh();
        self::assertSame(
            'Armat M41AE2 Heavy Pulse Rifle',
            $character->weapons[0]->name,
        );
        self::assertSame('M3 Personnel Armor', $character->armor->name);
        $character->delete();
    }

    public function testSaveGear(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marine',
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-gear'),
                [
                    'gear' => [
                        // Normal item.
                        'm314-motion-tracker',
                        // Not found.
                        'deck-of-cards',
                    ],
                ],
            )
            ->assertRedirect(route('alien.create', 'finish'));
        $character->refresh();
        self::assertSame('M314 Motion Tracker', $character->gear[0]->name);
        $character->delete();
    }

    public function testSaveGearRolledQuantity(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'career' => 'colonial-marshal',
            'owner' => $user->email,
        ]);
        DiceService::shouldReceive('rollOne')->times(1)->with(6)->andReturn(6);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-gear'),
                [
                    'gear' => [
                        'neversleep-pills',
                        '357-magnum-revolver',
                    ],
                ],
            )
            ->assertRedirect(route('alien.create', 'finish'));
        $character->refresh();
        // Pills have a variable amount.
        self::assertSame('Neversleep Pills', $character->gear[0]->name);
        self::assertSame(6, $character->gear[0]->quantity);
        $character->delete();
    }

    public function testCreateFinish(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'finish'))
            ->assertOk()
            ->assertSee('Finishing Touches');
        $character->delete();
    }

    public function testSaveFinish(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->postJson(
                route('alien.save-finish'),
                [
                    'agenda' => 'To save the cheerleader',
                    'appearance' => 'Generic white dude',
                    'buddy' => 'Bob King',
                    'rival' => 'Phil',
                ],
            )
            ->assertRedirect(route('alien.create', 'review'));
        $character->refresh();
        self::assertSame('To save the cheerleader', $character->agenda);
        self::assertSame('Generic white dude', $character->appearance);
        self::assertSame('Bob King', $character->buddy);
        self::assertSame('Phil', $character->rival);
        $character->delete();
    }

    public function testReviewIncomplete(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'review'))
            ->assertSee('You have not set your attributes')
            ->assertDontSee('Character looks good!');
        $character->delete();
    }

    public function testReviewComplete(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'agility' => 3,
            'armor' => 'm3-personnel-armor',
            'career' => 'colonial-marine',
            'empathy' => 3,
            'gear' => [
                ['id' =>  'm314-motion-tracker'],
            ],
            'name' => 'Bob King',
            'owner' => $user->email,
            'skills' => [
                'close-combat' => 3,
                'command' => 1,
                'comtech' => 0,
                'heavy-machinery' => 0,
                'manipulation' => 0,
                'medical-aid' => 0,
                'mobility' => 0,
                'observation' => 0,
                'piloting' => 0,
                'ranged-combat' => 3,
                'stamina' => 3,
                'survival' => 0,
            ],
            'strength' => 5,
            'talents' => [
                'banter',
            ],
            'wits' => 3,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->get(route('alien.create', 'review'))
            ->assertDontSee('You have not set your attributes')
            ->assertSee('Character looks good!');
        $character->delete();
    }

    public function testSaveCharacterIncomplete(): void
    {
        $user = User::factory()->create();
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        self::actingAs($user)
            ->withSession([CharactersController::SESSION_KEY => $character->id])
            ->post(route('alien.save-character'))
            ->assertRedirect(route('alien.create', 'review'));
        $character->delete();
    }

    public function testSaveCharacter(): void
    {
        $user = User::factory()->create();
        $partialCharacter = PartialCharacter::factory()->create([
            'agility' => 3,
            'armor' => 'm3-personnel-armor',
            'career' => 'colonial-marine',
            'empathy' => 3,
            'gear' => [
                ['id' =>  'm314-motion-tracker'],
            ],
            'name' => 'Save Test',
            'owner' => $user->email,
            'skills' => [
                'close-combat' => 3,
                'command' => 1,
                'comtech' => 0,
                'heavy-machinery' => 0,
                'manipulation' => 0,
                'medical-aid' => 0,
                'mobility' => 0,
                'observation' => 0,
                'piloting' => 0,
                'ranged-combat' => 3,
                'stamina' => 3,
                'survival' => 0,
            ],
            'strength' => 5,
            'talents' => [
                'banter',
            ],
            'wits' => 3,
        ]);
        $response = self::actingAs($user)
            ->withSession([
                CharactersController::SESSION_KEY => $partialCharacter->id,
            ])
            ->post(route('alien.save-character'));
        self::assertModelMissing($partialCharacter);
        $character = Character::where('name', 'Save Test')
            ->where('owner', $user->email)
            ->firstOrFail();
        self::assertSame('Save Test', $character->name);
        $response->assertRedirect(route('alien.character', $character->id));
        $character->delete();
    }

    public function testIndex(): void
    {
        $trusted = Role::create(['name' => 'trusted']);
        $trusted->givePermissionTo(Permission::create(['name' => 'view data']));
        $user = User::factory()->create();
        $user->assignRole($trusted);

        /** @var Character */
        $character1 = Character::factory()->create(['owner' => $user->email]);
        /** @var Character */
        $character2 = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->getJson(route('alien.characters.index'))
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

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'alien']);
        /** @var Character */
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'owner' => $user->email,
        ]);

        self::actingAs($user)
            ->getJson(route('alien.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name)
            ->assertJsonPath('data.campaign_id', $campaign->id);

        $character->delete();
    }

    public function testViewCharacter(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create(['owner' => $user->email]);

        $this->actingAs($user)
            ->get(route('alien.character', $character))
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);
        $character->delete();
    }
}
