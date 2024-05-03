<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Capers;

use App\Models\Capers\Character;
use App\Models\Capers\Identity;
use App\Models\Capers\PartialCharacter;
use App\Models\Capers\Power;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Tests for the Capers character controller.
 * @group capers
 * @group controllers
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    use WithFaker;

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
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get(
                route('capers.character', $character),
                ['character' => $character, 'user' => $user]
            )
            ->assertSee($user->email)
            ->assertSee(e($character->name), false);

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
            ->getJson(route('capers.character', $character))
            ->assertNotFound();
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
            ->get('/characters/capers/create')
            ->assertOk()
            ->assertSee('The basics');
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

        $character1 = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        $character2 = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get('/characters/capers/create')
            ->assertOk()
            ->assertSee('Choose character');

        $character1->delete();
        $character2->delete();
    }

    /**
     * Test choosing which character to continue.
     * @test
     */
    public function testCreateNewCharacterContinue(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($user)
            ->get(sprintf('/characters/capers/create/%s', $character->id))
            ->assertRedirect('/characters/capers/create/basics');

        $character->delete();
    }

    /**
     * Test trying to switch to a new character from an in-progress character.
     * @test
     */
    public function testCreateNewAfterContinuing(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/new')
            ->assertOk()
            ->assertSee('The basics')
            ->assertSessionHas('capers-partial', function ($value) use ($character) {
                return $value !== $character->id;
            });
        $character->delete();
    }

    /**
     * Test trying to set the basics for a character without sending any.
     * @test
     */
    public function testCreateBasicsEmpty(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('capers.create-basics'), [])
            ->assertSessionHasErrors(['name', 'nav', 'type']);
    }

    /**
     * Test trying to set the basics for a character.
     * @test
     */
    public function testCreateBasics(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        $name = $this->faker->name;
        self::actingAs($user)
            ->post(
                route('capers.create-basics'),
                [
                    'name' => $name,
                    'nav' => 'anchors',
                    'type' => 'caper',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                config('app.url') . '/characters/capers/create/anchors'
            );
        $character->refresh();

        self::assertSame($name, $character->name);
        self::assertSame(Character::TYPE_CAPER, $character->type);

        $character->delete();
    }

    /**
     * Test trying to load the anchors page.
     * @test
     */
    public function testAnchorsPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/anchors')
            ->assertOk()
            ->assertSee('Anchors');

        $character->delete();
    }

    /**
     * Test trying to set the anchors for a character without sending any.
     * @test
     */
    public function testCreateAnchorsEmpty(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)
            ->post(route('capers.create-anchors'), [])
            ->assertSessionHasErrors(['identity', 'nav', 'vice', 'virtue']);
    }

    /**
     * Test trying to save a character's anchors.
     * @test
     */
    public function testCreateAnchors(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::assertNull($character->identity);
        self::actingAs($user)
            ->post(
                route('capers.create-anchors'),
                [
                    'identity' => 'deviant',
                    'nav' => 'anchors',
                    'vice' => 'alcohol',
                    'virtue' => 'loyal',
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                config('app.url') . '/characters/capers/create/anchors'
            );
        $character->refresh();

        self::assertInstanceOf(Identity::class, $character->identity);

        $character->delete();
    }

    /**
     * Test trying to load the traits page.
     * @test
     */
    public function testTraitsPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/traits')
            ->assertOk()
            ->assertSee('Traits');

        $character->delete();
    }

    /**
     * Test trying to set a character's traits without choosing a high and low
     * one.
     * @test
     */
    public function testCreateTraitsNotChoosing(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-traits'),
                [
                    'nav' => 'skills',
                ]
            )
            ->assertSessionHasErrors(['trait-high', 'trait-low']);

        $character->delete();
    }

    /**
     * Test trying to set a character's traits to invalid attributes.
     * @test
     */
    public function testCreateTraitsInvalidAttributes(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-traits'),
                [
                    'nav' => 'skills',
                    'trait-high' => 'intelligence',
                    'trait-low' => 'willpower',
                ]
            )
            ->assertSessionHasErrors(['trait-high', 'trait-low']);

        $character->delete();
    }

    /**
     * Test trying to set a character's traits to valid attributes.
     * @test
     */
    public function testCreateTraits(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-traits'),
                [
                    'nav' => 'skills',
                    'trait-high' => 'strength',
                    'trait-low' => 'expertise',
                ]
            )
            ->assertSessionHasNoErrors();
        $character->refresh();

        self::assertSame(2, $character->agility);
        self::assertSame(2, $character->charisma);
        self::assertSame(1, $character->expertise);
        self::assertSame(2, $character->perception);
        self::assertSame(2, $character->resilience);
        self::assertSame(3, $character->strength);

        $character->delete();
    }

    /**
     * Test trying to load the skills page.
     * @test
     */
    public function testSkillsPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/skills')
            ->assertOk()
            ->assertSee('Skills');

        $character->delete();
    }

    /**
     * Test trying to save a character's skills.
     * @test
     */
    public function testCreateSkills(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::assertEmpty($character->skills);
        self::actingAs($user)
            ->post(
                route('capers.create-skills'),
                [
                    'nav' => 'skills',
                    'skills' => [
                        'guns',
                        'sense',
                    ],
                ]
            )
            ->assertSessionHasNoErrors();
        $character->refresh();
        self::assertNotEmpty($character->skills);

        $character->delete();
    }

    /**
     * Test trying to load the perks page as an exceptional.
     * @test
     */
    public function testPerksPageExceptional(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'type' => Character::TYPE_EXCEPTIONAL,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/perks')
            ->assertStatus(Response::HTTP_NOT_IMPLEMENTED);

        $character->delete();
    }

    /**
     * Test trying to load the perks page as a caper.
     * @test
     */
    public function testPerksPageCaper(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'type' => Character::TYPE_CAPER,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/perks')
            ->assertRedirect('/characters/capers/create/basics')
            ->assertSessionHasErrors([
                'type' => 'Only Exceptionals can choose perks.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to load the powers page as an exceptional.
     * @test
     */
    public function testPowersPageExceptional(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'type' => Character::TYPE_EXCEPTIONAL,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/powers')
            ->assertRedirect('/characters/capers/create/basics')
            ->assertSessionHasErrors([
                'type' => 'Only Capers can choose powers.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to load the powers page.
     * @test
     */
    public function testPowersPageCaper(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'powers' => [
                'alter-form' => [
                    'id' => 'alter-form',
                    'rank' => 1,
                ],
            ],
            'owner' => $user->email,
            'type' => Character::TYPE_CAPER,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/powers')
            ->assertOk()
            ->assertSee('Powers');

        $character->delete();
    }

    /**
     * Test trying to save a character's powers if they shouldn't have them.
     * @test
     */
    public function testCreatePowersExceptional(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_EXCEPTIONAL,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'animal-affinity',
                    ],
                    'options' => 'one-major',
                ]
            )
            ->assertSessionHasErrors([
                'type' => 'Only Capers can choose powers.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to save a character's powers with an invalid choice of the
     * number and type of powers.
     * @test
     */
    public function testCreatePowersInvalidChoice(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'animal-affinity',
                    ],
                    'options' => 'your-mom',
                ]
            )
            ->assertSessionHasErrors();
        $character->refresh();

        self::assertEmpty($character->powers);

        $character->delete();
    }

    /**
     * Test trying to save a character's powers with one major power.
     * @test
     */
    public function testCreatePowersOneMajor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::assertEmpty($character->powers);
        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'animal-affinity',
                    ],
                    'options' => 'one-major',
                ]
            )
            ->assertSessionHasNoErrors();
        $character->refresh();

        self::assertNotEmpty($character->powers);
        /** @var Power */
        $power = $character->powers['animal-affinity'];
        self::assertSame(1, $power->rank);

        $character->delete();
    }

    /**
     * Test trying to add two major powers when choosing the one-major option.
     * @test
     */
    public function testCreatePowersTwoMajorChoseOneMajor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'animal-affinity',
                        'dimension-step',
                    ],
                    'options' => 'one-major',
                ]
            )
            ->assertSessionHasErrors([
                'powers' => 'You can only choose a *single* power.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to add a single minor power when choosing the one-major
     * option.
     * @test
     */
    public function testCreatePowersOneMinorChoseOneMajor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'cold-beam',
                    ],
                    'options' => 'one-major',
                ]
            )
            ->assertSessionHasErrors([
                'powers' => 'You must choose one *major* power.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to add multiple minor powers after choosing to have one at
     * rank 2.
     * @test
     */
    public function testCreatePowersTwoMinorsChoseOneMinor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'acid-stream',
                        'cold-beam',
                    ],
                    'options' => 'one-minor',
                ]
            )
            ->assertSessionHasErrors([
                'powers' => 'You can only choose a *single* power.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to add a major power after choosing to have one minor power.
     * @test
     */
    public function testCreatePowersOneMajorChoseOneMinor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'animal-affinity',
                    ],
                    'options' => 'one-minor',
                ]
            )
            ->assertSessionHasErrors([
                'powers' => 'You must choose one *minor* power.',
            ]);

        $character->delete();
    }

    /**
     * Test choosing to have two minor powers but only selecting one.
     * @test
     */
    public function testCreatePowersOneMinorChoseTwoMinors(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'acid-stream',
                    ],
                    'options' => 'two-minor',
                ]
            )
            ->assertSessionHasErrors([
                'powers' => 'You must choose *two* minor powers.',
            ]);

        $character->delete();
    }

    /**
     * Test choosing to have two minor powers but choosing two major powers.
     * @test
     */
    public function testCreatePowersTwoMajorsChoseTwoMinors(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'animal-affinity',
                        'dimension-step',
                    ],
                    'options' => 'two-minor',
                ]
            )
            ->assertSessionHasErrors([
                'powers' => 'You can only choose two *minor* powers.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to save a character's powers if they have a rank 2 minor.
     * @test
     */
    public function testCreatePowersOneMinor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::assertEmpty($character->powers);
        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'alter-form',
                    ],
                    'options' => 'one-minor',
                ]
            )
            ->assertSessionHasNoErrors();
        $character->refresh();

        self::assertNotEmpty($character->powers);
        /** @var Power */
        $power = $character->powers['alter-form'];
        self::assertSame(2, $power->rank);

        $character->delete();
    }

    /**
     * Test trying to save a character's powers if they have two rank 1 minors.
     * @test
     */
    public function testCreatePowersTwoMinor(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::assertEmpty($character->powers);
        self::actingAs($user)
            ->post(
                route('capers.create-powers'),
                [
                    'nav' => 'skills',
                    'powers' => [
                        'alter-form',
                        'body-armor',
                    ],
                    'options' => 'two-minor',
                ]
            )
            ->assertSessionHasNoErrors();
        $character->refresh();

        self::assertCount(2, $character->powers);
        /** @var Power */
        $alterForm = $character->powers['alter-form'];
        self::assertSame(1, $alterForm->rank);
        /** @var Power */
        $bodyArmor = $character->powers['body-armor'];
        self::assertSame(1, $bodyArmor->rank);

        $character->delete();
    }

    /**
     * Test trying to load the boosts page as an exceptional.
     * @test
     */
    public function testBoostsPageExceptional(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'type' => Character::TYPE_EXCEPTIONAL,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/boosts')
            ->assertRedirect('/characters/capers/create/basics')
            ->assertSessionHasErrors([
                'type' => 'Only Capers can choose boosts.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to load the boosts page as a caper that hasn't chosen a
     * power yet.
     * @test
     */
    public function testPowersPageCaperNoPowers(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'type' => Character::TYPE_CAPER,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/boosts')
            ->assertRedirect('/characters/capers/create/powers')
            ->assertSessionHasErrors([
                'type' => 'You must choose powers before boosts.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to load the boosts page as a caper that has chosen a power.
     * @test
     */
    public function testBoostsPageCaper(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'powers' => [
                'alter-form' => [
                    'boosts' => [
                        'density-decrease-boost',
                    ],
                    'id' => 'alter-form',
                    'rank' => 1,
                ],
            ],
            'owner' => $user->email,
            'type' => Character::TYPE_CAPER,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/boosts')
            ->assertOk()
            ->assertSee('Boosts');

        $character->delete();
    }

    /**
     * Test trying to save a character's boosts if they can't even have powers.
     *
     * The character shouldn't have even been able to add a power in the first
     * place, but whatever.
     * @test
     */
    public function testCreateBoostsExceptional(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_EXCEPTIONAL,
            'owner' => $user->email,
            'powers' => [
                'alter-form' => [
                    'id' => 'alter-form',
                    'rank' => 2,
                ],
            ],
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-boosts'),
                [
                    'nav' => 'skills',
                    'boosts' => [
                        'alter-form+density-decrease-boost',
                        'alter-form+density-increase-boost',
                        'alter-form+gaseous-form-boost',
                        'alter-form+liquid-form-boost',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'type' => 'Only Capers can choose powers.',
            ]);

        $character->delete();
    }

    /**
     * Test trying to store boosts for a caper that doesn't have any powers.
     * @test
     */
    public function testCreateBoostsNoPowers(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-boosts'),
                [
                    'nav' => 'skills',
                    'boosts' => [
                        'alter-form+density-decrease-boost',
                        'alter-form+density-increase-boost',
                        'alter-form+gaseous-form-boost',
                        'alter-form+liquid-form-boost',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'boosts' => 'Character does not have required power '
                    . '"alter-form" for boost ID "density-decrease-boost".',
            ]);
        self::assertEmpty($character->powers);

        $character->delete();
    }

    /**
     * Test trying to store too many boosts for a power.
     * @test
     */
    public function testCreateBoostsTooMany(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'powers' => [
                'alter-form' => [
                    'id' => 'alter-form',
                    'rank' => 1,
                ],
            ],
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        /** @var Power */
        $power = $character->powers['alter-form'];
        self::assertEmpty($power->boosts);
        self::actingAs($user)
            ->post(
                route('capers.create-boosts'),
                [
                    'nav' => 'skills',
                    'boosts' => [
                        'alter-form+density-decrease-boost',
                        'alter-form+density-increase-boost',
                        'alter-form+gaseous-form-boost',
                        'alter-form+liquid-form-boost',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'boosts' => 'Power "Alter form" must have 3 boosts.',
            ]);
        $character->refresh();

        /** @var Power */
        $power = $character->powers['alter-form'];
        self::assertEmpty($power->boosts);

        $character->delete();
    }

    /**
     * Test trying to add a boost that doesn't work with a given power.
     * @test
     */
    public function testCreateBoostsInvalid(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'powers' => [
                'alter-form' => [
                    'id' => 'alter-form',
                    'rank' => 1,
                ],
            ],
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->post(
                route('capers.create-boosts'),
                [
                    'nav' => 'skills',
                    'boosts' => [
                        'alter-form+density-decrease-boost',
                        'alter-form+density-increase-boost',
                        'alter-form+invalid-boost',
                    ],
                ]
            )
            ->assertSessionHasErrors([
                'boosts' => 'Boost ID "invalid-boost" is not available for '
                    . 'power "Alter form".',
            ]);

        $character->refresh();
        /** @var Power */
        $power = $character->powers['alter-form'];
        self::assertEmpty($power->boosts);

        $character->delete();
    }

    /**
     * Test adding boosts to a caper's power.
     * @test
     */
    public function testCreateBoosts(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'powers' => [
                'alter-form' => [
                    'id' => 'alter-form',
                    'rank' => 1,
                ],
            ],
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        /** @var Power */
        $power = $character->powers['alter-form'];
        self::assertEmpty($power->boosts);
        self::actingAs($user)
            ->post(
                route('capers.create-boosts'),
                [
                    'nav' => 'skills',
                    'boosts' => [
                        'alter-form+density-decrease-boost',
                        'alter-form+density-increase-boost',
                        'alter-form+gaseous-form-boost',
                    ],
                ]
            )
            ->assertSessionHasNoErrors();
        $character->refresh();

        /** @var Power */
        $power = $character->powers['alter-form'];
        self::assertNotEmpty($power->boosts);

        $character->delete();
    }

    /**
     * Test trying to load the gear page.
     * @test
     */
    public function testGearPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'gear' => [
                ['id' => 'mens-tie', 'quantity' => 1],
            ],
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/gear')
            ->assertOk()
            ->assertSee('Gear')
            // The character has bought a tie for $5.
            ->assertSee('145');

        $character->delete();
    }

    /**
     * Test saving some gear.
     * @test
     */
    public function testCreateGear(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::assertEmpty($character->gear);
        self::actingAs($user)
            ->post(
                route('capers.create-gear'),
                [
                    'nav' => 'skills',
                    'gear' => [
                        'mens-tie',
                        'womens-skirt',
                    ],
                    'quantity' => [
                        2,
                        0,
                    ],
                ]
            )
            ->assertSessionHasNoErrors();
        $character->refresh();

        self::assertCount(1, $character->gear);

        $character->delete();
    }

    /**
     * Test trying to load the review page.
     * @test
     */
    public function testReviewPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/review')
            ->assertOk()
            ->assertSee('No skills chosen');

        $character->delete();
    }

    /**
     * Test saving a character.
     * @test
     */
    public function testSave(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'name' => 'Save test',
            'type' => Character::TYPE_CAPER,
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        $response = self::actingAs($user)
            ->post(route('capers.create-save'), [])
            ->assertSessionHasNoErrors();
        self::assertModelMissing($character);
        /** @var Character */
        $character = Character::where('name', 'Save test')
            ->where('owner', $user->email)
            ->firstOrFail();
        self::assertSame('Save test', $character->name);
        $response->assertRedirect(route('capers.character', $character));

        $character->delete();
    }

    /**
     * Test trying to load an invalid page.
     * @test
     */
    public function testUnknownPage(): void
    {
        /** @var User */
        $user = User::factory()->create();

        /** @var PartialCharacter */
        $character = PartialCharacter::factory()->create([
            'owner' => $user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        session(['capers-partial' => $character->id]);

        self::actingAs($user)
            ->get('/characters/capers/create/invalid')
            ->assertNotFound();

        $character->delete();
    }
}
