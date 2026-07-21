<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Modules\Shadowrun5e\Http\Controllers\CharactersController;
use Modules\Shadowrun5e\Http\Requests\AttributesRequest;
use Modules\Shadowrun5e\Http\Requests\BackgroundRequest;
use Modules\Shadowrun5e\Http\Requests\KnowledgeSkillsRequest;
use Modules\Shadowrun5e\Http\Requests\MartialArtsRequest;
use Modules\Shadowrun5e\Http\Requests\QualitiesRequest;
use Modules\Shadowrun5e\Http\Requests\RulesRequest;
use Modules\Shadowrun5e\Http\Requests\SkillsRequest;
use Modules\Shadowrun5e\Http\Requests\SocialRequest;
use Modules\Shadowrun5e\Http\Requests\StandardPriorityRequest;
use Modules\Shadowrun5e\Http\Requests\VitalsRequest;
use Modules\Shadowrun5e\Http\Resources\CharacterResource;
use Modules\Shadowrun5e\Models\ActiveSkill;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\KnowledgeSkill;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

use function array_shift;
use function route;

#[CoversClass(AttributesRequest::class)]
#[CoversClass(BackgroundRequest::class)]
#[CoversClass(CharacterResource::class)]
#[CoversClass(CharactersController::class)]
#[CoversClass(KnowledgeSkillsRequest::class)]
#[CoversClass(MartialArtsRequest::class)]
#[CoversClass(QualitiesRequest::class)]
#[CoversClass(RulesRequest::class)]
#[CoversClass(SkillsRequest::class)]
#[CoversClass(SocialRequest::class)]
#[CoversClass(StandardPriorityRequest::class)]
#[CoversClass(VitalsRequest::class)]
#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Character::where('system', 'shadowrun5e')->delete();
    }

    #[Test]
    #[TestDox('Unauthenticated users can not view characters index')]
    public function testUnauthenticated(): void
    {
        self::getJson(route('shadowrun5e.characters.index'))
            ->assertUnauthorized();
    }

    #[Test]
    #[TestDox('Loading Shadowrun 5E characters returns an empty data array for users without characters')]
    public function testAuthenticatedNoCharacters(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    #[Test]
    #[TestDox('Characters from other systems do not show in Shadowrun 5E characters index')]
    public function testAuthenticatedNoCharactersFromSystem(): void
    {
        $user = User::factory()->create();

        Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'cyberpunkred',
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJson(['data' => []]);
    }

    #[Test]
    #[TestDox('Shadowrun 5E characters appear in Shadowrun 5E characters index')]
    public function testAuthenticatedWithSR5ECharacter(): void
    {
        $user = User::factory()->create();
        $character2 = Character::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character2->_id,
                'handle' => $character2->handle,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'system' => 'shadowrun5e',
            ]);
    }

    #[Test]
    #[TestDox('Users with no characters show an empty characters page')]
    public function testListCharactersIfTheyHaveNone(): void
    {
        self::actingAs(User::factory()->create())
            ->get(route('shadowrun5e.characters'))
            ->assertSee('You don\'t have any characters!', false)
            ->assertSee('Shadowrun 5E Characters');
    }

    #[Test]
    #[TestDox('Shadowrun 5E sum-to-ten characters appear with correct JSON keys')]
    public function testShowCharacterSumToTen(): void
    {
        $user = User::factory()->create();

        $character = Character::factory()->create([
            'owner' => $user->email->address,
            'priorities' => [
                'metatype' => 'dwarf',
                'metatypePriority' => 'C',
                'magicPriority' => 'E',
                'attributePriority' => 'B',
                'skillPriority' => 'C',
                'resourcePriority' => 'B',
                'magic' => 'mundane',
                'gameplay' => 'established',
            ],
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.show', $character))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'handle' => $character->handle,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'priorities' => [
                    'metatype' => 'dwarf',
                    'metatype_priority' => 'C',
                    'magic_priority' => 'E',
                    'attribute_priority' => 'B',
                    'skill_priority' => 'C',
                    'resource_priority' => 'B',
                    'magic' => 'mundane',
                    'gameplay' => 'established',
                ],
                'system' => 'shadowrun5e',
            ]);
    }

    #[Test]
    #[TestDox('Standard priority Shadowrun 5E characters are converted to JSON correctly')]
    public function testShowCharacterStandard(): void
    {
        $user = User::factory()->create();

        $character = Character::factory()->create([
            'owner' => $user->email->address,
            'priorities' => [
                'a' => 'attributes',
                'b' => 'skills',
                'c' => 'resources',
                'd' => 'metatype',
                'e' => 'magic',
                'metatype' => 'human',
                'magic' => '',
                'gameplay' => 'established',
            ],
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.show', $character))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $character->_id,
                'handle' => $character->handle,
                'owner' => [
                    'id' => $user->id,
                    'name' => $user->name,
                ],
                'priorities' => [
                    'a' => 'attributes',
                    'b' => 'skills',
                    'c' => 'resources',
                    'd' => 'metatype',
                    'e' => 'magic',
                    'metatype' => 'human',
                    'magic' => '',
                    'gameplay' => 'established',
                ],
                'system' => 'shadowrun5e',
            ]);
    }

    #[Test]
    #[TestDox('Showing a character from another system returns a 404')]
    public function testShowCharacterOtherSystem(): void
    {
        $user = User::factory()->create();
        $character = Character::factory()->create([
            'owner' => $user->email->address,
            'system' => 'shadowrun6e',
        ]);

        self::actingAs($user)
            ->getJson(route('shadowrun5e.characters.show', $character))
            ->assertNotFound();
    }

    #[Test]
    #[TestDox('Users can view Shadowrun 5E characters')]
    public function testViewCharacter(): void
    {
        $user = User::factory()->create();

        $character = Character::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->get(route('shadowrun5e.character', $character))
            ->assertSee($character->handle);
    }

    #[Test]
    #[TestDox('Unauthenticated users can view Shadowrun 5E characters')]
    public function testViewPartialCharacterNoLogin(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::get(route('shadowrun5e.character', $character))
            ->assertOk()
            ->assertSee($character->handle);
    }

    #[Test]
    #[TestDox('Users can resume char gen for a partial character')]
    public function testCreateResumeCharacter(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->get(route('shadowrun5e.create', $character->id))
            ->assertRedirectToRoute('shadowrun5e.create', 'rules');
    }

    #[Test]
    #[TestDox('Users can create a new Shadowrun 5E character')]
    public function testCreateNewCharacter(): void
    {
        $user = User::factory()->create();

        $characters = PartialCharacter::where('owner', $user->email->address)
            ->get();
        self::assertCount(0, $characters);
        self::actingAs($user)
            ->get(route('shadowrun5e.create'))
            ->assertOk()
            ->assertSee('Rules');

        $characters = PartialCharacter::where('owner', $user->email->address)
            ->get();
        self::assertNotNull($characters[0]);
    }

    #[Test]
    #[TestDox('Users can choose which partially created character to resume building')]
    public function testCreateNewCharacterChoose(): void
    {
        $user = User::factory()->create();

        PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'system' => 'shadowrun5e',
        ]);
        PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'system' => 'shadowrun5e',
        ]);

        self::actingAs($user)
            ->get(route('shadowrun5e.create'))
            ->assertOk()
            ->assertSee('Choose character');
    }

    #[Test]
    #[TestDox('Users must choose rules for a new Shadowrun 5E character')]
    public function testCreateRulesMissingFields(): void
    {
        self::actingAs(User::factory()->create())
            ->post(route('shadowrun5e.create-rules'), [])
            ->assertSessionHasErrors(['gameplay', 'nav', 'rulebook', 'system']);
    }

    #[Test]
    #[TestDox('Users can not create a Shadowrun 5E character without the core rulebook')]
    public function testCreateRulesNoCoreBook(): void
    {
        self::actingAs(User::factory()->create())
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

    #[Test]
    #[TestDox('Users can not skip the initial character creation step')]
    public function testCreateRulesNoCharacter(): void
    {
        self::actingAs(User::factory()->create())
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

    #[Test]
    #[TestDox('Users can choose rules, gameplay, and priority for a new character')]
    public function testCreateRules(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'priorities');

        $character->refresh();
        self::assertNotNull($character->priorities);
        self::assertArrayHasKey('system', $character->priorities);
        self::assertSame('priority', $character->priorities['system']);
        self::assertSame('established', $character->priorities['gameplay']);
        self::assertSame('core', $character->priorities['rulebooks']);
    }

    #[Test]
    #[TestDox('Users must choose rules before setting priorities for a new character')]
    public function testLoadPrioritiesPageWithNoRules(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'priorities'))
            ->assertRedirectToRoute('shadowrun5e.create', 'rules')
            ->assertSessionHasErrors();
    }

    #[Test]
    #[TestDox('Loading the priorities page in char gen without rules redirects to the rules page')]
    public function testLoadPrioritiesPageInvalidCharGenSystem(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'priorities' => [
                'gameplay' => 'established',
                'rulebooks' => 'core,forbidden-arcana',
                'system' => 'sum-to-ten',
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'priorities'))
            ->assertRedirectToRoute('shadowrun5e.create', 'rules')
            ->assertSessionHasErrors();
    }

    #[Test]
    #[TestDox('Users can load priorities page for partial characters')]
    public function testLoadPrioritiesPage(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'priorities' => [
                'gameplay' => 'established',
                'rulebooks' => 'core,forbidden-arcana',
                'system' => 'priority',
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'priorities'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Standard priority')
            ->assertSee('Previous: Rules')
            ->assertSee('Next: Vitals');
    }

    #[Test]
    #[TestDox('Users can not leave priorities blank during character generation')]
    public function testStorePrioritiesNoData(): void
    {
        self::actingAs(User::factory()->create())
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

    #[Test]
    #[TestDox('Users can not choose invalid priorities during character generation')]
    public function testStorePrioritiesInvalidValues(): void
    {
        self::actingAs(User::factory()->create())
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

    #[Test]
    #[TestDox('Users can not choose both magician and magic at priority E')]
    public function testStorePrioritiesInvalidMagic(): void
    {
        self::actingAs(User::factory()->create())
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

    #[Test]
    #[TestDox('storing priorities with a priority level that requires choosing an awakened discipline fails without the magic field')]
    public function testStorePrioritiesWithoutMagic(): void
    {
        self::actingAs(User::factory()->create())
            ->post(
                route('shadowrun5e.create-standard'),
                ['priority-a' => 'magic']
            )
            ->assertSessionHasErrors([
                'magic' => 'The magic field is required.',
            ]);
    }

    #[Test]
    #[TestDox('Users can store valid priorities for new Shadowrun 5E characters')]
    public function testStorePriorities(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'vitals');

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
    }

    #[Test]
    #[TestDox('Users can load the vitals page for a new character')]
    public function testLoadVitals(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'vitals'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Handle')
            ->assertSee('Real name')
            ->assertSee('Next: Attributes')
            ->assertSee('Previous: Priorities');
    }

    #[Test]
    #[TestDox('Users can not submit vitals without a handle for the runner')]
    public function testStoreVitalsNoData(): void
    {
        self::actingAs(User::factory()->create())
            ->post(route('shadowrun5e.create-vitals'), [])
            ->assertSessionHasErrors(['handle']);
    }

    #[Test]
    #[TestDox('Users can store vitals for new Shadowrun 5E characters')]
    public function testStoreVitals(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'attributes');

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
    }

    /**
     * Test loading the attributes page.
     */
    public function testLoadAttributes(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'a' => 'magic',
                'b' => 'attributes',
                'e' => 'metatype',
                'magic' => 'magician',
                'metatype' => 'human',
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'attributes'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Vitals')
            ->assertSee('Next: Qualities');
    }

    /**
     * Test storing attributes without sending any.
     */
    public function testStoreAttributesNoData(): void
    {
        self::actingAs(User::factory()->create())
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

    #[Test]
    #[TestDox('Users can store attributes for new Shadowrun 5E characters')]
    public function testStoreAttributes(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'qualities');

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
    }

    /**
     * Test loading the qualities page for a character that has already
     * selected a quality, but doesn't have Run and Gun enabled (for nav).
     */
    public function testLoadQualitiesAlreadyChoseOne(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'qualities' => [
                [
                    'id' => 'lucky',
                ],
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'qualities'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Lucky')
            ->assertSee('Previous: Attributes')
            ->assertSee('Next: Skills');
    }

    /**
     * Test loading the qualities page for a character with Run and Gun enabled.
     */
    public function testLoadQualitiesNone(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'priorities' => [
                'rulebooks' => 'core,run-and-gun',
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'qualities'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Attributes')
            ->assertSee('Next: Martial-arts');
    }

    /**
     * Test storing qualities with a valid but not found quality.
     */
    public function testStoreQualitiesNotFound(): void
    {
        self::actingAs(User::factory()->create())
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'nav' => 'next',
                    'quality' => ['not-found'],
                ],
            )
            ->assertSessionHasErrors([
                'quality.0' => 'Quality ID "not-found" is invalid.',
            ]);
    }

    /**
     * Test storing qualities with an invalid quality ID.
     */
    public function testStoreQualitiesNotValid(): void
    {
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'quality' => ['*(0)'],
                ]
            )
            ->assertSessionHasErrors([
                'quality.0' => 'The quality ID was invalid.',
            ]);
    }

    /**
     * Test storing a normal quality.
     */
    public function testStoreQuality(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->post(
                route('shadowrun5e.create-qualities'),
                [
                    'nav' => 'next',
                    'quality' => ['impassive'],
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('shadowrun5e.create', 'skills');

        $character->refresh();
        $qualities = $character->getQualities();
        self::assertNotNull($qualities[0]);
        self::assertSame('Impassive', $qualities[0]->name);
    }

    /**
     * Test storing an allergy quality with the extra info.
     */
    public function testStoreAllergy(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'skills');

        $character->refresh();
        $qualities = $character->getQualities();
        self::assertNotNull($qualities[0]);
        self::assertSame('Allergy (Uncommon Mild - hay and grass)', $qualities[0]->name);
    }

    /**
     * Test storing an addiction quality with the extra info.
     */
    public function testStoreAddiction(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'attributes');

        $character->refresh();
        $qualities = $character->getQualities();
        self::assertNotNull($qualities[0]);
        self::assertSame('Addiction (Mild - alcohol)', $qualities[0]->name);
    }

    /**
     * Test storing knowledge skills.
     */
    public function testStoreKnowledgeSkills(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'augmentations');

        $character->refresh();
        $skills = $character->getKnowledgeSkills();
        self::assertCount(3, $skills);

        /** @var KnowledgeSkill $skill */
        $skill = $skills[0];
        self::assertSame('interests', $skill->category);
        self::assertSame('Alcohol', $skill->name);
        self::assertSame(1, $skill->level);
        self::assertNull($skill->specialization);
    }

    /**
     * Test trying to get to the martial arts page if the user chose not to
     * allow Run and Gun as a rulebook.
     */
    public function testMartialArtsWithoutRunAndGun(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'martial-arts'))
            ->assertRedirectToRoute('shadowrun5e.create', 'rules')
            ->assertSessionHasErrors();
    }

    /**
     * Test trying to get the martial arts page with Run and Gun allowed.
     */
    public function testLoadMartialArts(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'rulebooks' => 'core,run-and-gun',
            ],
            'martialArts' => [
                'styles' => [
                    'Aikido',
                ],
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'martial-arts'))
            ->assertOk()
            ->assertSee('Aikido')
            ->assertSee('Previous: Qualities')
            ->assertSee('Next: Skills');
    }

    /**
     * Test storing martial arts with an invalid style.
     */
    public function testStoreMartialArtsStyleInvalid(): void
    {
        self::actingAs(User::factory()->create())
            ->post(
                route('shadowrun5e.create-martial-arts'),
                ['style' => 'not-found'],
            )
            ->assertSessionHasErrors([
                'style' => 'Martial Arts Style ID "not-found" is invalid',
            ]);
    }

    /**
     * Test storing martial arts with an invalid technique.
     */
    public function testStoreMartialArtsTechniqueInvalid(): void
    {
        self::actingAs(User::factory()->create())
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
     */
    public function testStoreMartialArts(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'skills');

        $character->refresh();
        $styles = $character->getMartialArtsStyles();
        $techniques = $character->getMartialArtsTechniques();
        self::assertNotNull($styles[0]);
        self::assertSame('Aikido', $styles[0]->name);
        self::assertNotNull($techniques[0]);
        self::assertSame('Called Shot', $techniques[0]->name);
        self::assertNotNull($techniques[1]);
        self::assertSame('Constrictor\'s Crush', $techniques[1]->name);
    }

    /**
     * Test overwriting a character's previously chosen martial arts.
     */
    public function testMartialArtsOverwrite(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'martialArts' => [
                'styles' => ['foo'],
                'techniques' => ['called-shot-disarm'],
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->post(
                route('shadowrun5e.create-martial-arts'),
                ['nav' => 'prev']
            )
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('shadowrun5e.create', 'qualities');

        $character->refresh();
        $styles = $character->getMartialArtsStyles();
        $techniques = $character->getMartialArtsTechniques();
        self::assertCount(0, $styles);
        self::assertCount(0, $techniques);
    }

    /**
     * Test loading the skills page with a skill and Run and Gun not enabled.
     */
    public function testLoadSkillsPageWithSkill(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
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
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'skills'))
            ->assertOk()
            ->assertSee('Automatics')
            ->assertSee('+2 AK-97')
            ->assertSee('Previous: Qualities')
            ->assertSee('Next: Knowledge');
    }

    /**
     * Test loading the skills page without any skills, but with Run and Gun
     * enabled.
     */
    public function testLoadASkillsPageNoSkills(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'priorities' => [
                'rulebooks' => 'core,run-and-gun',
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'skills'))
            ->assertOk()
            ->assertSee('Previous: Martial-arts')
            ->assertSee('Next: Knowledge');
    }

    /**
     * Test storing an invalid skill group.
     */
    public function testStoreSkillsInvalidGroup(): void
    {
        self::actingAs(User::factory()->create())
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
     */
    public function testStoreSkillsInvalidSkill(): void
    {
        self::actingAs(User::factory()->create())
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
     */
    public function testStoreSkills(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
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
            ->assertRedirectToRoute('shadowrun5e.create', 'knowledge');

        $character->refresh();
        $groups = $character->getSkillGroups();
        self::assertSame('Electronics', $groups[0]->name);

        $skills = (array)$character->getSkills();

        /** @var ActiveSkill $skill */
        $skill = array_shift($skills);
        self::assertSame('Blades', $skill->name);
        self::assertSame(1, $skill->level);
        self::assertNull($skill->specialization);

        /** @var ActiveSkill $skill */
        $skill = array_shift($skills);
        self::assertSame('Automatics', $skill->name);
        self::assertSame(2, $skill->level);
        self::assertSame('AK-97', $skill->specialization);
    }

    #[Test]
    #[TestDox('A mundane character shows the correct next link on the knowledge page')]
    public function testLoadKnowledgeSkillsMundane(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'knowledgeSkills' => [
                [
                    'category' => 'interests',
                    'name' => 'Elven Wine',
                    'level' => 4,
                ],
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'knowledge'))
            ->assertOk()
            ->assertSee('Elven Wine')
            ->assertSee('Next: Augmentations')
            ->assertDontSee('Next: Magic')
            ->assertDontSee('Next: Resonance');
    }

    /**
     * Test loading the knowledge skills page, with a magical character (for
     * navigation).
     */
    public function testLoadKnowledgeSkillsMagical(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'magician',
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'knowledge'))
            ->assertOk()
            ->assertDontSee('Next: Augmentations')
            ->assertSee('Next: Magic')
            ->assertDontSee('Next: Resonance');
    }

    /**
     * Test loading the knowledge skills page, with a technomancer (for
     * navigation).
     */
    public function testLoadKnowledgeSkillsResonance(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'technomancer',
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'knowledge'))
            ->assertOk()
            ->assertDontSee('Next: Augmentations')
            ->assertDontSee('Next: Magic')
            ->assertSee('Next: Resonance');
    }

    /**
     * Test trying to load the magic page as a mundane character.
     */
    public function testLoadMagicMundane(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'magic'))
            ->assertRedirect(route('shadowrun5e.create', 'priority'))
            ->assertSessionHasErrors();
    }

    /**
     * Test trying to load the magic page as a mage.
     */
    public function testLoadMagicAsMage(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'magician',
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'magic'))
            ->assertOk()
            ->assertSee('Next: Augmentations')
            ->assertSee('Previous: Knowledge');
    }

    /**
     * Test loading resonance as a mundane character.
     */
    public function testLoadResonanceMundane(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'resonance'))
            ->assertRedirect(route('shadowrun5e.create', 'priority'))
            ->assertSessionHasErrors();
    }

    /**
     * Test trying to load the resonance page as a technomancer.
     */
    public function testLoadResonanceTechnomancer(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'technomancer',
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'resonance'))
            ->assertOk()
            ->assertSee('Next: Augmentations')
            ->assertSee('Previous: Knowledge');
    }

    /**
     * Test loading the augmentations page as a mundane character.
     */
    public function testLoadAugmentationsMundane(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'augmentations' => [
                [
                    'id' => 'bone-lacing-aluminum',
                ],
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'augmentations'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Bone Lacing')
            ->assertSee('Previous: Knowledge')
            ->assertSee('Next: Weapons');
    }

    /**
     * Test loading the augmentations page as a magical character.
     */
    public function testLoadAugmentationsMagical(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'magician',
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'augmentations'))
            ->assertOk()
            ->assertSee('Next: Weapons')
            ->assertSee('Previous: Magic');
    }

    /**
     * Test loading the augmentations page as a technomancer.
     */
    public function testLoadAugmentationsTechno(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'priorities' => [
                'magic' => 'technomancer',
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'augmentations'))
            ->assertOk()
            ->assertSee('Previous: Resonance')
            ->assertSee('Next: Weapons');
    }

    /**
     * Test loading the weapons page.
     */
    public function testLoadWeapons(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'weapons' => [
                ['id' => 'ak-98'],
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'weapons'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('AK-98')
            ->assertSee('Previous: Augmentations')
            ->assertSee('Next: Armor');
    }

    /**
     * Test loading the armor page.
     */
    public function testLoadArmor(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'armor' => [
                ['id' => 'armor-jacket'],
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'armor'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Weapons')
            ->assertSee('Next: Gear');
    }

    /**
     * Test loading the gear page.
     */
    public function testLoadGear(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'gear' => [
                ['id' => 'ear-buds-1'],
            ],
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'gear'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Ear Buds')
            ->assertSee('Previous: Armor')
            ->assertSee('Next: Vehicles');
    }

    /**
     * Test loading the vehicles page.
     */
    public function testLoadVehicles(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'vehicles' => [
                ['id' => 'dodge-scoot'],
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'vehicles'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Gear')
            ->assertSee('Next: Social');
    }

    /**
     * Test loading the social page.
     */
    public function testLoadSocial(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'social'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Vehicles')
            ->assertSee('Next: Background');
    }

    public function testLoadSocialFriendsInHighPlaces(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
            'qualities' => [
                ['id' => 'friends-in-high-places'],
            ],
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'social'))
            ->assertSee('Friends in High Places');
    }

    public function testStoreSocial(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        $contacts = [
            'contact-archetypes' => [
                'bartender',
            ],
            'contact-connections' => [
                5,
            ],
            'contact-loyalties' => [
                3,
            ],
            'contact-names' => [
                'Brian Flanigan',
            ],
            'contact-notes' => [
                null,
            ],
            'nav' => 'next',
        ];
        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->postJson(
                route('shadowrun5e.create-social'),
                $contacts,
            )
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('shadowrun5e.create', 'background');

        $character->refresh();
        self::assertCount(1, (array)$character->contacts);
    }

    /**
     * Test loading the background page.
     */
    public function testLoadBackground(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'background'))
            ->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSee('Previous: Social')
            ->assertSee('Next: Review');
    }

    /**
     * Test storing a character's background.
     */
    public function testStoreBackground(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'background' => ['gender' => 'male'],
            'owner' => $user->email->address,
        ]);

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
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->post(route('shadowrun5e.create-background'), $background)
            ->assertSessionHasNoErrors()
            ->assertRedirectToRoute('shadowrun5e.create', 'review');

        $character->refresh();

        // Navigation is part of the request, but isn't part of the background.
        unset($background['nav']);
        // And the character's gender was already set.
        $background = ['gender' => 'male'] + $background;

        self::assertSame($background, $character->background);
    }

    /**
     * Test loading the review page.
     */
    public function testLoadReview(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'review'))
            ->assertOk()
            //->assertSee('Previous: Background')
            //->assertSee('Next: Save')
            ->assertSessionHasNoErrors();
    }

    public function testSaveForLater(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.save-for-later'))
            ->assertRedirectToRoute('dashboard')
            ->assertSessionMissing('shadowrun5e-partial')
            ->assertSessionHasNoErrors();
    }

    /**
     * Test trying to go to an invalid creation step.
     */
    public function testInvalidCreationStep(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);
        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'unknown'))
            ->assertNotFound();
    }

    /**
     * Test trying to create a new character when we've already selected one.
     */
    public function testStartNewCharacter(): void
    {
        $user = User::factory()->create();

        $character = PartialCharacter::factory()->create([
            'owner' => $user->email->address,
        ]);

        self::actingAs($user)
            ->withSession(['shadowrun5e-partial' => $character->id])
            ->get(route('shadowrun5e.create', 'new'))
            ->assertOk()
            ->assertSee('Creation system')
            ->assertSessionHas(
                'shadowrun5e-partial',
                function (string $value) use ($character): bool {
                    return $value !== $character->id;
                }
            );
    }

    /**
     * Test trying to update a character without being logged in.
     */
    public function testUpdateUnauthenticated(): void
    {
        $character = Character::factory()->create([]);
        self::patchJson(route('shadowrun5e.characters.update', $character))
            ->assertUnauthorized();
    }

    /**
     * Test trying to update a character that isn't part of a campaign.
     */
    public function testUpdateCharacterWithoutCampaign(): void
    {
        $user = User::factory()->create();

        $character = Character::factory()->create([]);

        self::actingAs($user)
            ->patchJson(route('shadowrun5e.characters.update', $character))
            ->assertForbidden()
            ->assertSee('Only characters in campaigns can be updated this way');
    }

    /**
     * Test trying to update a character that's part of a campaign, but the user
     * isn't the GM.
     */
    public function testUpdateNotGm(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['system' => 'shadowrun5e']);
        $character = Character::factory()->create(['campaign_id' => $campaign]);

        self::actingAs($user)
            ->patchJson(route('shadowrun5e.characters.update', $character))
            ->assertForbidden()
            ->assertSee('You can not update another user\'s character', false);
    }

    /**
     * Test trying to patch a character with an invalid patch document.
     */
    public function testUpdateInvalidPatch(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        $character = Character::factory()->create(['campaign_id' => $campaign]);

        self::actingAs($user)
            ->patch(route('shadowrun5e.characters.update', $character))
            ->assertBadRequest()
            ->assertSee('Unable to extract patch operations from \'null\'', true);
    }

    /**
     * Test trying to patch a character with a valid patch document using an
     * invalid operation.
     */
    public function testUpdateInvalidPatchOperation(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        $character = Character::factory()->create(['campaign_id' => $campaign]);

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
            ->assertBadRequest()
            ->assertSee('No operation set for patch operation');
    }

    /**
     * Test trying to patch a character using an invalid path.
     */
    public function testUpdateInvalidPath(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        $character = Character::factory()->create([
            'campaign_id' => $campaign,
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
            ->assertBadRequest()
            ->assertSee('Pointer starts with invalid character');
    }

    #[Test]
    #[TestDox('Giving a character enough stun damage will kill them')]
    public function testUpdateLotsOfStun(): void
    {
        $user = User::factory()->create();

        $campaign = Campaign::factory()->create([
            'gm' => $user->id,
            'system' => 'shadowrun5e',
        ]);

        $character = Character::factory()->create([
            'body' => 4,
            'campaign_id' => $campaign,
            'willpower' => 4,
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
            ->assertOk()
            ->assertJson([
                'data' => [
                    'damage_overflow' => 35,
                    'damage_physical' => 10,
                    'damage_stun' => 10,
                ],
            ]);
    }
}
