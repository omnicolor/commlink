<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Subversion;

use App\Models\Subversion\Character;
use App\Models\Subversion\PartialCharacter;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

#[Group('subversion')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }

    public function testCreateNewCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->get('/characters/subversion/create')
            ->assertOk()
            ->assertSee('Each character chooses one lineage and one lineage option');
        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);

        // @phpstan-ignore-next-line
        $characters[0]->delete();
    }

    public function testCreateCharacterChoose(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $character1 = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $character2 = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get('/characters/subversion/create')
            ->assertOk()
            ->assertSee('Choose character');

        $character1->delete();
        $character2->delete();
    }

    public function testCreateContinueCharacterByPath(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get(route('subversion.create', $character->id))
            ->assertRedirect(route('subversion.create-lineage'));

        $character->delete();
    }

    public function testCreateContinueCharacterBySession(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create'))
            ->assertOk()
            ->assertSee('Each character chooses one lineage and one lineage option');

        $character->delete();
    }

    public function testCreateNewCharacterAfterStartingAnother(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/subversion/create/new')
            ->assertOk()
            ->assertSee('Each character chooses one lineage and one lineage option')
            ->assertSessionHas(
                'subversion-partial',
                function ($value) use ($character): bool {
                    return $value !== $character->id;
                },
            );
        $character->delete();
    }

    public function testCreateBackground(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'background'))
            ->assertOk()
            ->assertSee('The final component of your PC’s identity is their background');

        $character->delete();
    }

    public function testStoreBackground(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->postJson(
                route('subversion.create-background'),
                ['background' => 'agriculturist']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'caste'));

        $character->refresh();
        self::assertSame('agriculturist', $character->background?->id);
        $character->delete();
    }

    public function testCreateCaste(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'caste'))
            ->assertOk()
            ->assertSee('Caste in Neo Babylon is your status in life—it determines not');

        $character->delete();
    }

    public function testStoreCaste(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->postJson(
                route('subversion.create-caste'),
                ['caste' => 'lower']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'ideology'));

        $character->refresh();
        self::assertSame('lower', $character->caste?->id);
        $character->delete();
    }

    public function testCreateHooks(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'hooks'))
            ->assertOk()
            ->assertSee('motivations and situations particular to them');
        $character->delete();
    }

    public function testStoreHooks(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->postJson(
                route('subversion.create-hooks'),
                [
                    // @phpstan-ignore-next-line
                    'hook1' => $this->faker->catchPhrase(),
                    // @phpstan-ignore-next-line
                    'hook2' => $this->faker->catchPhrase(),
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'relations'));

        $character->refresh();
        self::assertCount(2, $character->hooks);
        $character->delete();
    }

    public function testCreateIdeology(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'ideology'))
            ->assertOk()
            ->assertSee('Ideology and Values are the fundamental beliefs of the PC.');
        $character->delete();
    }

    public function testStoreIdeology(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->postJson(
                route('subversion.create-ideology'),
                ['ideology' => 'neo-anarchist']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'values'));

        $character->refresh();
        self::assertSame('neo-anarchist', $character->ideology?->id);
        $character->delete();
    }

    public function testCreateImpulse(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'impulse'))
            ->assertOk()
            ->assertSee('If Values are a character\'s motivations driven by their beliefs', false);
        $character->delete();
    }

    public function testStoreImpulse(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->postJson(
                route('subversion.create-impulse'),
                ['impulse' => 'indulgence']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'hooks'));

        $character->refresh();
        self::assertSame('indulgence', $character->impulse?->id);
        $character->delete();
    }

    public function testStoreLineage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        $name = $this->faker->name;
        self::actingAs($user)
            ->postJson(
                route('subversion.create-lineage'),
                [
                    'name' => $name,
                    'lineage' => 'dwarven',
                    'option' => 'toxin-resistant',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'origin'));

        $character->refresh();
        self::assertSame($name, $character->name);
        self::assertSame('dwarven', $character->lineage?->id);
        self::assertSame('toxin-resistant', $character->lineage_option);
        $character->delete();
    }

    public function testStoreInvalidLineage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        $name = $this->faker->name;
        self::actingAs($user)
            ->postJson(
                route('subversion.create-lineage'),
                [
                    'name' => $name,
                    'lineage' => 'invalid',
                    'option' => 'does not matter',
                ]
            )
            ->assertJson([
                'message' => 'The selected lineage is invalid. (and 1 more error)',
                'errors' => [
                    'lineage' => [
                        'The selected lineage is invalid.',
                    ],
                    'option' => [
                        'Lineage option can\'t be verified on unknown lineage.',
                    ],
                ],
            ])
            ->assertUnprocessable();

        $character->delete();
    }

    public function testStoreValidLineageWithInvalidOption(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        $name = $this->faker->name;
        self::actingAs($user)
            ->postJson(
                route('subversion.create-lineage'),
                [
                    'name' => $name,
                    'lineage' => 'dwarven',
                    'option' => 'invalid',
                ]
            )
            ->assertJson([
                'message' => 'Lineage option is not valid for lineage.',
                'errors' => [
                    'option' => [
                        'Lineage option is not valid for lineage.',
                    ],
                ],
            ])
            ->assertUnprocessable();

        $character->delete();
    }

    public function testCreateOrigin(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'origin'))
            ->assertOk()
            ->assertSee('Origin represents the culture in which you were raised or with');

        $character->delete();
    }

    public function testCreateInvalid(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'invalid'))
            ->assertNotFound();

        $character->delete();
    }

    public function testStoreOrigin(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->postJson(
                route('subversion.create-origin'),
                ['origin' => 'altaipheran']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'background'));

        $character->refresh();
        self::assertSame('altaipheran', $character->origin?->id);
        $character->delete();
    }

    public function testCreateRelations(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'relations'))
            ->assertOk()
            ->assertSee('determine their connections to other NPCs');

        $character->delete();
    }

    public function testCreateValues(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'values'))
            ->assertOk()
            ->assertSee('In addition to the beliefs that line up with their ideology');

        $character->delete();
    }

    public function testStoreValues(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        $corrupted = $this->faker->boolean();
        self::actingAs($user)
            ->postJson(
                route('subversion.create-values'),
                [
                    'corrupted' => $corrupted,
                    // @phpstan-ignore-next-line
                    'value1' => $this->faker->catchPhrase(),
                    // @phpstan-ignore-next-line
                    'value2' => $this->faker->catchPhrase(),
                    // @phpstan-ignore-next-line
                    'value3' => $this->faker->catchPhrase(),
                ],
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('subversion.create', 'impulse'));

        $character->refresh();
        self::assertCount(3, $character->values);
        self::assertSame($corrupted, $character->corrupted_value);
        $character->delete();
    }

    public function testSaveForLater(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['subversion-partial' => $character->id]);

        self::actingAs($user)
            ->get(route('subversion.create', 'later'))
            ->assertSee('Choose character');
        self::assertNull(session('subversion-partial'));
    }

    public function testIndex(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create(['owner' => $user->email]);
        self::actingAs($user)
            ->getJson(route('subversion.characters.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', $character->name);
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create(['owner' => $user->email]);
        self::actingAs($user)
            ->getJson(route('subversion.characters.show', $character))
            ->assertOk()
            ->assertJsonPath('data.name', $character->name);
    }

    public function testView(): void
    {
        $user = User::factory()->create();
        /** @var Character */
        $character = Character::factory()->create(['owner' => $user->email]);

        self::actingAs($user)
            ->get(
                route('subversion.character', $character),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }
}
