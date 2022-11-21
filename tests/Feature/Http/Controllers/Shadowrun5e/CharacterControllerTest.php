<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\Campaign;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\KnowledgeSkill;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Controller for the Shadowrun 5E characters controller.
 * @group shadowrun
 * @group shadowrun5e
 * @group controllers
 * @medium
 */
final class CharacterControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test loading Shadowrun 5E characters if unauthenticated.
     * @test
     */
    public function testUnauthenticated(): void
    {
        self::getJson(route('shadowrun5e.characters.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading Shadowrun 5E characters if authenticated but the user
     * doesn't have any characters.
     * @test
     */
    public function testAuthenticatedNoCharacters(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    /**
     * Test loading Shadowrun 5E characters if authenticated, but all of the
     * user's characters are for different systems.
     * @test
     */
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'cyberpunkred',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);

        $character->delete();
    }

    /**
     * Test loading Shadowrun 5E characters if authenticated, and the user has
     * a character for SR5E (in addition to another system).
     * @test
     */
    public function testAuthenticatedWithSR5ECharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character1 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        /** @var Character */
        $character2 = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'handle' => $character2->handle,
                'owner' => $user->email,
                'system' => 'shadowrun5e',
                'updated_at' => $character2->updated_at,
                'created_at' => $character2->created_at,
            ]);

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test listing a user's Shadowrun characters.
     * @test
     */
    public function testListCharactersIfTheyHaveNone(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->get('/characters/shadowrun5e')
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('Shadowrun 5E Characters');
    }

    /**
     * Test loading an individual character.
     * @test
     */
    public function testShowCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.show', $character->id))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'handle' => $character->handle,
                'owner' => $user->email,
                'system' => 'shadowrun5e',
                'updated_at' => $character->updated_at,
                'created_at' => $character->created_at,
            ]);

        $character->delete();
    }

    /**
     * Test loading an individual character from a different system.
     * @test
     */
    public function testShowCharacterOtherSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun6e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.show', $character->id))
            ->assertNotFound();

        $character->delete();
    }

    /**
     * Test loading a character view.
     * @test
     */
    public function testViewCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get(
                \sprintf('/characters/shadowrun5e/%s', $character->id),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($character->handle);

        $character->delete();
    }

    /**
     * Test trying to view a partial character without being logged in.
     * @test
     */
    public function testViewPartialCharacterNoLogin(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::get(
            \sprintf('/characters/shadowrun5e/%s', $character->id),
            ['character' => $character]
        )
            ->assertOk()
            ->assertSee($character->handle);

        $character->delete();
    }

    /**
     * Test trying to resume building a new character.
     * @test
     */
    public function testCreateResumeCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get(\sprintf('/characters/shadowrun5e/create/%s', $character->id))
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/rules');

        $character->delete();
    }

    /**
     * Test trying to create a brand new character.
     * @test
     */
    public function testCreateNewCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(0, $characters);
        self::actingAs($user)
            ->get('/characters/shadowrun5e/create')
            ->assertOk()
            ->assertSee('Rules');

        $characters = PartialCharacter::where('owner', $user->email)->get();
        self::assertCount(1, $characters);

        // @phpstan-ignore-next-line
        $characters[0]->delete();
    }

    /**
     * Test trying to resume building a character if the user has multiple.
     * @test
     */
    public function testCreateNewCharacterChoose(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character1 = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        /** @var PartialCharacter */
        $character2 = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create')
            ->assertOk()
            ->assertSee('Choose character');

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test choosing rules for a new character without sending any.
     * @test
     */
    public function testCreateRulesMissingFields(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('shadowrun5e.create-rules'), [])
            ->assertSessionHasErrors(['gameplay', 'nav', 'rulebook', 'system']);
    }

    /**
     * Test choosing rules for a new character without including the core
     * rulebook.
     * @test
     */
    public function testCreateRulesNoCoreBook(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-rules'),
                [
                    'gameplay' => 'established',
                    'nav' => 'next',
                    'rulebook' => ['forbidden-arcana'],
                    'system' => 'priority',
                ],
            )
            ->assertSessionHasErrors([
                'rulebook' => 'The core rulebook is required.',
            ]);
    }

    /**
     * Test choosing rules for a new character without starting the character.
     * @test
     */
    public function testCreateRulesNoCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-rules'),
                [
                    'gameplay' => 'established',
                    'nav' => 'next',
                    'rulebook' => ['core'],
                    'system' => 'priority',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertNotFound();
    }

    /**
     * Test choosing rules for a new character.
     * @test
     */
    public function testCreateRules(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-rules'),
                [
                    'gameplay' => 'established',
                    'nav' => 'next',
                    'rulebook' => ['core'],
                    'system' => 'priority',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                config('app.url') . '/characters/shadowrun5e/create/priorities'
            );
        $character->refresh();
        // @phpstan-ignore-next-line
        self::assertSame('priority', $character->priorities['system']);
        self::assertSame('established', $character->priorities['gameplay']);
        self::assertSame('core', $character->priorities['rulebooks']);

        $character->delete();
    }

    /**
     * Test trying to choose priorities without setting the rules.
     * @test
     */
    public function testLoadPrioritiesPageWithNoRules(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/priorities')
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/rules')
            ->assertSessionHasErrors();

        $character->delete();
    }

    /**
     * Test trying to load the priorities page for a character that has set up
     * the rules.
     * @test
     */
    public function testLoadPrioritiesPageInvalidCharGenSystem(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'priorities' => [
                'gameplay' => 'established',
                'rulebooks' => 'core,forbidden-arcana',
                'system' => 'sum-to-ten',
            ],
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/priorities')
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/rules')
            ->assertSessionHasErrors();

        $character->delete();
    }

    /**
     * Test trying to load the priorities page for a character that has set up
     * the rules.
     * @test
     */
    public function testLoadPrioritiesPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'priorities' => [
                'gameplay' => 'established',
                'rulebooks' => 'core,forbidden-arcana',
                'system' => 'priority',
            ],
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/priorities')
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Standard priority')
            ->assertSee('Previous: Rules')
            ->assertSee('Next: Vitals');

        $character->delete();
    }

    /**
     * Test storing priorities without sending any.
     * @test
     */
    public function testStorePrioritiesNoData(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('shadowrun5e.create-standard'), [])
            ->assertSessionHasErrors([
                'metatype' => 'The metatype field is required.',
                'priority-a' => 'The priority-a field is required.',
                'priority-b' => 'The priority-b field is required.',
                'priority-c' => 'The priority-c field is required.',
                'priority-d' => 'The priority-d field is required.',
                'priority-e' => 'The priority-e field is required.',
            ]);
    }

    /**
     * Test storing priorities with invalid values.
     * @test
     */
    public function testStorePrioritiesInvalidValues(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-standard'),
                [
                    'magic' => 'dwarf',
                    'metatype' => 'magician',
                    'priority-a' => 'foo',
                    'priority-b' => 'foo',
                    'priority-c' => 'foo',
                    'priority-d' => 'foo',
                    'priority-e' => 'foo',
                ]
            )
            ->assertSessionHasErrors([
                'metatype' => 'The selected metatype is invalid.',
                'priority-a' => 'The selected priority-a is invalid.',
                'priority-b' => 'The selected priority-b is invalid.',
                'priority-c' => 'The selected priority-c is invalid.',
                'priority-d' => 'The selected priority-d is invalid.',
                'priority-e' => 'The selected priority-e is invalid.',
            ]);
    }

    /**
     * Test storing priorities with an invalid magic combination.
     * @test
     */
    public function testStorePrioritiesInvalidMagic(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-standard'),
                [
                    'magic' => 'magician',
                    'priority-e' => 'magic',
                ]
            )
            ->assertSessionHasErrors([
                'magic' => 'Magician is not a valid magic selection for priority E.',
            ]);
    }

    /**
     * Test storing priorities with a priority level that requires choosing
     * an awakened discipline.
     * @test
     */
    public function testStorePrioritiesWithoutMagic(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-standard'),
                [
                    'priority-a' => 'magic',
                ]
            )
            ->assertSessionHasErrors([
                'magic' => 'The magic field is required.',
            ]);
    }

    /**
     * Test storing some valid priorities.
     * @test
     */
    public function testStorePriorities(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-standard'),
                [
                    'magic' => 'technomancer',
                    'metatype' => 'human',
                    'nav' => 'next',
                    'priority-a' => 'magic',
                    'priority-b' => 'attributes',
                    'priority-c' => 'resources',
                    'priority-d' => 'skills',
                    'priority-e' => 'metatype',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/vitals');

        $character->refresh();
        self::assertSame(
            [
                'a' => 'magic',
                'b' => 'attributes',
                'c' => 'resources',
                'd' => 'skills',
                'e' => 'metatype',
                'magic' => 'technomancer',
                'metatype' => 'human',
            ],
            $character->priorities
        );

        $character->delete();
    }

    /**
     * Test loading the vitals page.
     * @test
     */
    public function testLoadVitals(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/vitals')
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Handle')
            ->assertSee('Real name')
            ->assertSee('Next: Attributes')
            ->assertSee('Previous: Priorities');

        $character->delete();
    }

    /**
     * Test storing vitals without sending any.
     * @test
     */
    public function testStoreVitalsNoData(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('shadowrun5e.create-vitals'), [])
            ->assertSessionHasErrors(['handle']);
    }

    /**
     * Test storing vitals for a character.
     * @test
     */
    public function testStoreVitals(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-vitals'),
                [
                    'birthdate' => '2030-04-01',
                    'birthplace' => 'Seattle',
                    'eyes' => 'chrome',
                    'gender' => 'other',
                    'handle' => 'Slamm-O',
                    'hair' => 'bald',
                    'height' => 1.87,
                    'nav' => 'next',
                    'real-name' => 'Bob Smith',
                    'weight' => 95,
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/attributes');

        $character->refresh();
        self::assertSame(
            [
                'birthdate' => '2030-04-01',
                'birthplace' => 'Seattle',
                'eyes' => 'chrome',
                'gender' => 'other',
                'hair' => 'bald',
                'height' => 1.87,
                'weight' => 95,
            ],
            $character->background
        );
        self::assertSame('Slamm-O', $character->handle);
        self::assertSame('Bob Smith', $character->realName);

        $character->delete();
    }

    /**
     * Test loading the attributes page.
     * @test
     */
    public function testLoadAttributes(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'a' => 'magic',
                'b' => 'attributes',
                'e' => 'metatype',
                'magic' => 'magician',
                'metatype' => 'human',
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/attributes')
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Vitals')
            ->assertSee('Next: Qualities');

        $character->delete();
    }

    /**
     * Test storing attributes without sending any.
     * @test
     */
    public function testStoreAttributesNoData(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('shadowrun5e.create-attributes'), [])
            ->assertSessionHasErrors([
                'agility',
                'body',
                'charisma',
                'edge',
                'intuition',
                'logic',
                'reaction',
                'strength',
                'willpower',
            ]);
    }

    /**
     * Test storing attributes.
     * @test
     */
    public function testStoreAttributes(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-attributes'),
                [
                    'agility' => 1,
                    'body' => 2,
                    'charisma' => 3,
                    'intuition' => 4,
                    'logic' => 5,
                    'reaction' => 6,
                    'strength' => 1,
                    'willpower' => 2,
                    'edge' => 4,
                    'nav' => 'next',
                    'magic' => 6,
                    'resonance' => 6,
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/qualities');

        $character->refresh();
        self::assertSame(1, $character->agility);
        self::assertSame(2, $character->body);
        self::assertSame(3, $character->charisma);
        self::assertSame(4, $character->intuition);
        self::assertSame(5, $character->logic);
        self::assertSame(6, $character->reaction);
        self::assertSame(1, $character->strength);
        self::assertSame(2, $character->willpower);
        self::assertNull($character->magic);
        self::assertNull($character->resonance);

        $character->delete();
    }

    /**
     * Test loading the qualities page for a character that has already
     * selected a quality, but doesn't have Run and Gun enabled (for nav).
     * @test
     */
    public function testLoadQualitiesAlreadyChoseOne(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'qualities' => [
                [
                    'id' => 'lucky',
                ],
            ],
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/qualities')
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Lucky')
            ->assertSee('Previous: Attributes')
            ->assertSee('Next: Skills');

        $character->delete();
    }

    /**
     * Test loading the qualities page for a character with Run and Gun enabled.
     * @test
     */
    public function testLoadQualitiesNone(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'priorities' => [
                'rulebooks' => 'core,run-and-gun',
            ],
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/qualities')
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Attributes')
            ->assertSee('Next: Martial-arts');

        $character->delete();
    }

    /**
     * Test storing qualities with a valid but not found quality.
     * @test
     */
    public function testStoreQualitiesNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'quality' => [
                        'not-found',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'quality.0' => 'Quality ID "not-found" is invalid.',
            ]);
    }

    /**
     * Test storing qualities with an invalid quality ID.
     * @test
     */
    public function testStoreQualitiesNotValid(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'quality' => [
                        '*(0)',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'quality.0' => 'The quality ID was invalid.',
            ]);
    }

    /**
     * Test storing a normal quality.
     * @test
     */
    public function testStoreQuality(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'nav' => 'next',
                    'quality' => [
                        'impassive',
                    ],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/skills');

        $character->refresh();
        $qualities = $character->getQualities();
        // @phpstan-ignore-next-line
        self::assertSame('Impassive', $qualities[0]->name);

        $character->delete();
    }

    /**
     * Test storing an allergy quality with the extra info.
     * @test
     */
    public function testStoreAllergy(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'nav' => 'next',
                    'quality' => [
                        'allergy-uncommon-mild_hay_and_grass',
                    ],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/skills');

        $character->refresh();
        $qualities = $character->getQualities();
        // @phpstan-ignore-next-line
        self::assertSame('Allergy (Uncommon Mild - hay and grass)', $qualities[0]->name);

        $character->delete();
    }

    /**
     * Test storing an addiction quality with the extra info.
     * @test
     */
    public function testStoreAddiction(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'nav' => 'prev',
                    'quality' => [
                        'addiction-mild_alcohol',
                    ],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/attributes');

        $character->refresh();
        $qualities = $character->getQualities();
        // @phpstan-ignore-next-line
        self::assertSame('Addiction (Mild - alcohol)', $qualities[0]->name);

        $character->delete();
    }

    /**
     * Test storing knowledge skills.
     * @test
     */
    public function testStoreKnowledgeSkills(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-knowledge-skills'),
                [
                    'nav' => 'next',
                    'skill-categories' => [
                        'interests',
                        'language',
                        'academic',
                    ],
                    'skill-levels' => [
                        1,
                        'N',
                        2,
                    ],
                    'skill-names' => [
                        'Alcohol',
                        'English',
                        '20th Century Movies',
                    ],
                    'skill-specializations' => [
                        0 => null,
                        1 => null,
                        2 => 'Marvel',
                    ],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/augmentations');

        $character->refresh();
        $skills = $character->getKnowledgeSkills();
        self::assertCount(3, $skills);

        /** @var KnowledgeSkill */
        $skill = $skills[0];
        self::assertSame('interests', $skill->category);
        self::assertSame('Alcohol', $skill->name);
        self::assertSame(1, $skill->level);
        self::assertNull($skill->specialization);

        $character->delete();
    }

    /**
     * Test trying to get to the martial arts page if the user chose not to
     * allow Run and Gun as a rulebook.
     * @test
     */
    public function testMartialArtsWithoutRunAndGun(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/martial-arts')
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/rules')
            ->assertSessionHasErrors();

        $character->delete();
    }

    /**
     * Test trying to get the martial arts page with Run and Gun allowed.
     * @test
     */
    public function testLoadMartialArts(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'rulebooks' => 'core,run-and-gun',
            ],
            'martialArts' => [
                'styles' => [
                    'Aikido',
                ],
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/martial-arts')
            ->assertOk()
            ->assertSee('Aikido')
            ->assertSee('Previous: Qualities')
            ->assertSee('Next: Skills');

        $character->delete();
    }

    /**
     * Test storing martial arts with an invalid style.
     * @test
     */
    public function testStoreMartialArtsStyleInvalid(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-martial-arts'),
                [
                    'style' => 'not-found',
                ]
            )
            ->assertSessionHasErrors([
                'style' => 'Martial Arts Style ID "not-found" is invalid',
            ]);
    }

    /**
     * Test storing martial arts with an invalid technique.
     * @test
     */
    public function testStoreMartialArtsTechniqueInvalid(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-martial-arts'),
                [
                    'style' => 'aikido',
                    'techniques' => [
                        'not-found',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'techniques.0' => 'Martial Arts Technique ID "not-found" is invalid',
            ]);
    }

    /**
     * Test storing a martial art.
     * @test
     */
    public function testStoreMartialArts(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-martial-arts'),
                [
                    'nav' => 'next',
                    'style' => 'aikido',
                    'techniques' => [
                        'called-shot-disarm',
                        'constrictors-crush',
                    ],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/skills');

        $character->refresh();
        $styles = $character->getMartialArtsStyles();
        $techniques = $character->getMartialArtsTechniques();
        // @phpstan-ignore-next-line
        self::assertSame('Aikido', $styles[0]->name);
        // @phpstan-ignore-next-line
        self::assertSame('Called Shot', $techniques[0]->name);
        // @phpstan-ignore-next-line
        self::assertSame('Constrictor\'s Crush', $techniques[1]->name);

        $character->delete();
    }

    /**
     * Test overwriting a character's previously chosen martial arts.
     * @test
     */
    public function testMartialArtsOverwrite(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'martialArts' => [
                'styles' => ['foo'],
                'techniques' => ['called-shot-disarm'],
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-martial-arts'),
                [
                    'nav' => 'prev',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/qualities');

        $character->refresh();
        $styles = $character->getMartialArtsStyles();
        $techniques = $character->getMartialArtsTechniques();
        self::assertCount(0, $styles);
        self::assertCount(0, $techniques);

        $character->delete();
    }

    /**
     * Test loading the skills page with a skill and Run and Gun not enabled.
     * @test
     */
    public function testLoadSkillsPageWithSkill(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'skills' => [
                [
                    'id' => 'automatics',
                    'level' => 6,
                    'specialization' => 'AK-97',
                ],
                [
                    'id' => 'invalid-skill',
                    'level' => 4,
                ],
            ],
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/skills')
            ->assertOk()
            ->assertSee('Automatics')
            ->assertSee('+2 AK-97')
            ->assertSee('Previous: Qualities')
            ->assertSee('Next: Knowledge');

        $character->delete();
    }

    /**
     * Test loading the skills page without any skills, but with Run and Gun
     * enabled.
     * @test
     */
    public function testLaodASkillsPageNoSkills(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'priorities' => [
                'rulebooks' => 'core,run-and-gun',
            ],
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/skills')
            ->assertOk()
            ->assertSee('Previous: Martial-arts')
            ->assertSee('Next: Knowledge');

        $character->delete();
    }

    /**
     * Test storing an invalid skill group.
     * @test
     */
    public function testStoreSkillsInvalidGroup(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-skills'),
                [
                    'group-levels' => [
                        'a',
                    ],
                    'group-names' => [
                        'not-found',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'group-levels.0' => 'The group-levels.0 must be an integer.',
                'group-names.0' => 'Skill group ID "not-found" is invalid',
            ]);
    }

    /**
     * Test storing an invalid skill.
     * @test
     */
    public function testStoreSkillsInvalidSkill(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-skills'),
                [
                    'skill-levels' => [
                        'a',
                    ],
                    'skill-names' => [
                        'not-found',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'skill-levels.0' => 'The skill-levels.0 must be an integer.',
                'skill-names.0' => 'Skill ID "not-found" is invalid',
            ]);
    }

    /**
     * Test storing some skills.
     * @test
     */
    public function testStoreSkills(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-skills'),
                [
                    'nav' => 'next',
                    'group-levels' => [
                        4,
                    ],
                    'group-names' => [
                        'electronics',
                    ],
                    'skill-levels' => [
                        1,
                        2,
                    ],
                    'skill-names' => [
                        'blades',
                        'automatics',
                    ],
                    'skill-specializations' => [
                        1 => 'AK-97',
                    ],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/knowledge');

        $character->refresh();
        $groups = $character->getSkillGroups();
        self::assertSame('Electronics', $groups[0]->name);

        $skills = $character->getSkills();
        // @phpstan-ignore-next-line
        self::assertSame('Blades', $skills[0]->name);
        // @phpstan-ignore-next-line
        self::assertSame(1, $skills[0]->level);
        // @phpstan-ignore-next-line
        self::assertNull($skills[0]->specialization);
        // @phpstan-ignore-next-line
        self::assertSame('Automatics', $skills[1]->name);
        // @phpstan-ignore-next-line
        self::assertSame(2, $skills[1]->level);
        // @phpstan-ignore-next-line
        self::assertSame('AK-97', $skills[1]->specialization);

        $character->delete();
    }

    /**
     * Test loading the knowledge skills page, with a mundane character
     * (testing navigation).
     * @test
     */
    public function testLoadKnowledgeSkillsMundane(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'knowledgeSkills' => [
                [
                    'category' => 'interests',
                    'name' => 'Elven Wine',
                    'level' => 4,
                ],
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/knowledge')
            ->assertOk()
            ->assertSee('Elven Wine')
            ->assertSee('Next: Augmentations')
            ->assertDontSee('Next: Magic')
            ->assertDontSee('Next: Resonance');

        $character->delete();
    }

    /**
     * Test loading the knowledge skills page, with a magical character (for
     * navigation).
     * @est
     */
    public function testLoadKnowledgeSkillsMagical(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'magician',
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/knowledge')
            ->assertOk()
            ->assertDontSee('Next: Augmentations')
            ->assertSee('Next: Magic')
            ->assertDontSee('Next: Resonance');

        $character->delete();
    }

    /**
     * Test loading the knowledge skills page, with a technomancer (for
     * navigation).
     * @est
     */
    public function testLoadKnowledgeSkillsResonance(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'technomancer',
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/knowledge')
            ->assertOk()
            ->assertDontSee('Next: Augmentations')
            ->assertDontSee('Next: Magic')
            ->assertSee('Next: Resonance');

        $character->delete();
    }

    /**
     * Test trying to load the magic page as a mundane character.
     * @test
     */
    public function testLoadMagicMundane(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/magic')
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/priority')
            ->assertSessionHasErrors();

        $character->delete();
    }

    /**
     * Test trying to load the magic page as a mage.
     * @test
     */
    public function testLoadMagicAsMage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'magician',
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/magic')
            ->assertOk()
            ->assertSee('Next: Augmentations')
            ->assertSee('Previous: Knowledge');

        $character->delete();
    }

    /**
     * Test loading resonance as a mundane character.
     * @test
     */
    public function testLoadResonanceMundane(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/resonance')
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/priority')
            ->assertSessionHasErrors();

        $character->delete();
    }

    /**
     * Test trying to load the resonance page as a technomancer.
     * @test
     */
    public function testLoadResonanceTechnomancer(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'technomancer',
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/resonance')
            ->assertOk()
            ->assertSee('Next: Augmentations')
            ->assertSee('Previous: Knowledge');

        $character->delete();
    }

    /**
     * Test loading the augmentations page as a mundane character.
     * @test
     */
    public function testLoadAugmentationsMundane(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'augmentations' => [
                [
                    'id' => 'bone-lacing-aluminum',
                ],
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/augmentations')
            ->assertOk()
            ->assertSessionHasNoErrors()
            //->assertSee('Bone Lacing')
            ->assertSee('Previous: Knowledge')
            ->assertSee('Next: Weapons');

        $character->delete();
    }

    /**
     * Test loading the augmentations page as a magical character.
     * @test
     */
    public function testLoadAugmentationsMagical(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'magician',
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/augmentations')
            ->assertOk()
            ->assertSee('Next: Weapons')
            ->assertSee('Previous: Magic');

        $character->delete();
    }

    /**
     * Test loading the augmentations page as a technomancer.
     * @test
     */
    public function testLoadAugmentationsTechno(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'technomancer',
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/augmentations')
            ->assertOk()
            ->assertSee('Previous: Resonance')
            ->assertSee('Next: Weapons');

        $character->delete();
    }

    /**
     * Test loading the weapons page.
     * @test
     */
    public function testLoadWeapons(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'weapons' => [
                ['id' => 'ak-98'],
            ],
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/weapons')
            ->assertOk()
            ->assertSessionHasNoErrors()
            //->assertSee('AK-98')
            ->assertSee('Previous: Augmentations')
            ->assertSee('Next: Armor');

        $character->delete();
    }

    /**
     * Test loading the armor page.
     * @test
     */
    public function testLoadArmor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'armor' => [
                ['id' => 'armor-jacket'],
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/armor')
            ->assertOk()
            ->assertSessionHasNoErrors()
            //->assertSee('Armor Jacket')
            ->assertSee('Previous: Weapons')
            ->assertSee('Next: Gear');

        $character->delete();
    }

    /**
     * Test loading the gear page.
     * @test
     */
    public function testLoadGear(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'gear' => [
                ['id' => 'ear-buds-1'],
            ],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/gear')
            ->assertOk()
            ->assertSessionHasNoErrors()
            //->assertSee('Ear Buds')
            ->assertSee('Previous: Armor')
            ->assertSee('Next: Vehicles');

        $character->delete();
    }

    /**
     * Test loading the vehicles page.
     * @test
     */
    public function testLoadVehicles(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'vehicles' => [
                ['id' => 'dodge-scoot'],
            ],
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/vehicles')
            ->assertOk()
            ->assertSessionHasNoErrors()
            //->assertSee('Dodge Scoot')
            ->assertSee('Previous: Gear')
            ->assertSee('Next: Social');

        $character->delete();
    }

    /**
     * Test loading the social page.
     * @test
     */
    public function testLoadSocial(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/social')
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Vehicles')
            ->assertSee('Next: Background');

        $character->delete();
    }

    /**
     * Test loading the background page.
     * @test
     */
    public function testLoadBackground(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/background')
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Social')
            ->assertSee('Next: Review');

        $character->delete();
    }

    /**
     * Test storing a character's background.
     * @test
     */
    public function testStoreBackground(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'background' => ['gender' => 'male'],
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        $background = [
            'age' => 'Really old.',
            'appearance' => 'Hair everywhere.',
            'born' => 'At a very young age.',
            'description' => 'Funny sense of humor.',
            'education' => 'School of hard knocks.',
            'family' => 'I\'m sure there\'s one out there.',
            'gender-identity' => 'Hyper, almost toxically, masculine.',
            'goals' => 'Get rich or die trying.',
            'hate' => 'Evil corps.',
            'limitations' => 'None that I\'ve found.',
            'living' => 'Former adult entertainer.',
            'love' => 'Strong ales.',
            'married' => 'LOL, no.',
            'moral' => 'None to speak of.',
            'motivation' => 'I wanna roll lots of dice.',
            'name' => 'Phillip J. Fry',
            'nav' => 'next',
            'personality' => 'Quirky, and old.',
            'qualities' => 'Read my character sheet.',
            'religion' => 'LOL, just no.',
            'size' => 'Shaped like a bowling pin.',
            'why' => 'Why not?',
        ];
        self::actingAs($user)
            ->post(route('shadowrun5e.create-background'), $background)
            ->assertSessionHasNoErrors()
            ->assertRedirect(config('app.url') . '/characters/shadowrun5e/create/review');

        $character->refresh();

        // Navigation is part of the request, but isn't part of the background.
        unset($background['nav']);
        // And the character's gender was already set.
        $background = ['gender' => 'male'] + $background;

        self::assertSame($background, $character->background);

        $character->delete();
    }

    /**
     * Test loading the review page.
     * @test
     */
    public function testLoadReview(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/review')
            ->assertOk()
            //->assertSee('Previous: Background')
            //->assertSee('Next: Save')
            ->assertSessionHasNoErrors();

        $character->delete();
    }

    /**
     * Test trying to go to an invalid creation step.
     * @test
     */
    public function testInvalidCreationStep(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);
        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/unknown')
            ->assertNotFound();

        $character->delete();
    }

    /**
     * Test trying to create a new character when we've already selected one.
     * @test
     */
    public function testStartNewCharacter(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'system' => 'shadowrun5e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['shadowrun5epartial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/shadowrun5e/create/new')
            ->assertOk()
            ->assertSee('Creation system')
            ->assertSessionHas(
                'shadowrun5epartial',
                function (string $value) use ($character): bool {
                    return $value !== $character->id;
                }
            );

        $character->delete();
    }

    /**
     * Test trying to update a character without being logged in.
     * @test
     */
    public function testUpdateUnauthenticated(): void
    {
        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::patchJson(route('shadowrun5e.characters.update', $character))
            ->assertUnauthorized();

        $character->delete();
    }

    /**
     * Test trying to update a character that isn't part of a campaign.
     * @test
     */
    public function testUpdateCharacterWithoutCampaign(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Character */
        $character = Character::factory()->create([
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->patchJson(route('shadowrun5e.characters.update', $character))
            ->assertForbidden()
            ->assertSee('Only characters in campaigns can be updated this way');

        $character->delete();
    }

    /**
     * Test trying to update a character that's part of a campaign, but the user
     * isn't the GM.
     * @test
     */
    public function testUpdateNotGm(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);

        /** @var Character */
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->patchJson(route('shadowrun5e.characters.update', $character))
            ->assertForbidden()
            ->assertSee('You can not update another user\'s character', false);

        $character->delete();
    }

    /**
     * Test trying to patch a character with an invalid patch document.
     * @test
     */
    public function testUpdateInvalidPatch(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->patch(route('shadowrun5e.characters.update', $character))
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertSee('Unable to extract patch operations from \'null\'', true);

        $character->delete();
    }

    /**
     * Test trying to patch a character with a valid patch document using an
     * invalid operation.
     * @test
     */
    public function testUpdateInvalidPatchOperation(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->patch(
                route('shadowrun5e.characters.update', $character),
                [
                    'patch' => [
                        [
                            'path' => 'test',
                            'value' => '1',
                        ],
                    ],
                ],
            )
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertSee('No operation set for patch operation');

        $character->delete();
    }

    /**
     * Test trying to patch a character using an invalid path.
     * @test
     */
    public function testUpdateInvalidPath(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'campaign_id' => $campaign,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->patch(
                route('shadowrun5e.characters.update', $character),
                [
                    'patch' => [
                        [
                            'op' => 'replace',
                            'path' => 'test',
                            'value' => '1',
                        ],
                    ],
                ],
            )
            ->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertSee('Pointer starts with invalid character');

        $character->delete();
    }

    /**
     * Test killing a character with stun damage.
     * @test
     */
    public function testUpdateLotsOfStun(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var Campaign */
        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        /** @var Character */
        $character = Character::factory()->create([
            'body' => 4,
            'campaign_id' => $campaign,
            'willpower' => 4,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        // A character with willpower 4 gets 10 boxen of stun damage, leaving 90
        // for physical at 2:1. Take out 20 to fill up their 10 boxen of
        // physical damage, and they've got 70 stun to take into overflow at
        // 2:1, so expect overflow to be 35. Ouch, don't fight with dragons.
        self::actingAs($user)
            ->patch(
                route('shadowrun5e.characters.update', $character),
                [
                    'patch' => [
                        [
                            'op' => 'replace',
                            'path' => '/damageStun',
                            'value' => '100',
                        ],
                    ],
                ],
            )
            ->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'data' => [
                    'damageOverflow' => 35,
                    'damagePhysical' => 10,
                    'damageStun' => 10,
                ],
            ]);

        $character->delete();
    }
}
