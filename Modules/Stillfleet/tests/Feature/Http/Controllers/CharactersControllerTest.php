<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Http\Controllers;

use App\Features\Stillfleet as StillfleetFeature;
use App\Models\User;
use Laravel\Pennant\Feature;
use Modules\Stillfleet\Models\Character;
use Modules\Stillfleet\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function e;
use function route;
use function session;

#[Group('stillfleet')]
#[Medium]
final class CharactersControllerTest extends TestCase
{
    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        if (!isset($this->user)) {
            $this->user = User::factory()->create();
            Feature::for($this->user)->activate(StillfleetFeature::class);
        }
        session(['stillfleet-partial' => null]);
    }

    public function testViewCharacter(): void
    {
        $character = Character::factory()->create([
            'owner' => $this->user->email,
        ]);

        self::actingAs($this->user)
            ->get(
                route('stillfleet.character', $character),
                ['character' => $character, 'user' => $this->user]
            )
            ->assertSee($this->user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }

    public function testShowCharacterOtherSystem(): void
    {
        $character = Character::factory()->create([
            'owner' => $this->user->email,
            'system' => 'shadowrun6e',
        ]);
        self::actingAs($this->user)
            ->getJson(route('stillfleet.character', $character))
            ->assertNotFound();
        $character->delete();
    }

    public function testCharacterList(): void
    {
        $character = Character::factory()->create([
            'owner' => $this->user->email,
        ]);

        self::actingAs($this->user)
            ->get(route('stillfleet.list'))
            ->assertSee($this->user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }

    public function testCreateExplicitNew(): void
    {
        session(['stillfleet-partial' => 'existing']);
        self::actingAs($this->user)
            ->get(route('stillfleet.create', 'new'))
            ->assertRedirect(route('stillfleet.create', 'class'));
        self::assertNotSame('existing', session('stillfleet-partial'));
    }

    public function testCreateNew(): void
    {
        PartialCharacter::where('owner', $this->user->email)->delete();
        self::actingAs($this->user)
            ->get(route('stillfleet.create'))
            ->assertOk();
        self::assertNotNull(session('stillfleet-partial'));
    }

    public function testCreateChoose(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        self::actingAs($this->user)
            ->get(route('stillfleet.create'))
            ->assertOk()
            ->assertSee('Choose character');
        self::assertNull(session('stillfleet-partial'));
        $character->delete();
    }

    public function testResumeSpecific(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        self::actingAs($this->user)
            ->get(route('stillfleet.create', $character->id))
            ->assertRedirect(route('stillfleet.create', 'class'));
        self::assertSame($character->_id, session('stillfleet-partial'));
        $character->delete();
    }

    public function testResumeLast(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session(['stillfleet-partial' => $character->_id]);
        self::actingAs($this->user)
            ->get(route('stillfleet.create'))
            ->assertOk();
    }

    public function testInvalidCreationStep(): void
    {
        self::actingAs($this->user)
            ->get(route('stillfleet.create', 'unknown'))
            ->assertNotFound();
    }

    public function testNewClass(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session(['stillfleet-partial' => $character->id]);
        self::actingAs($this->user)
            ->get(route('stillfleet.create', 'class'))
            ->assertSee('Become a Banshee');
        $character->delete();
    }

    public function testKeepClass(): void
    {
        $character = PartialCharacter::create([
            'owner' => $this->user->email,
            'roles' => [
                [
                    'id' => 'banshee',
                    'level' => 1,
                    'powers' => [],
                ],
            ],
        ]);
        session(['stillfleet-partial' => $character->id]);
        self::actingAs($this->user)
            ->get(route('stillfleet.create', 'class'))
            ->assertSee('Remain a Banshee');
        $character->delete();
    }

    public function testSaveClass(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session(['stillfleet-partial' => $character->id]);
        self::actingAs($this->user)
            ->postJson(
                route('stillfleet.create-class'),
                ['role' => 'tremulant'],
            )
            ->assertRedirect(route('stillfleet.create', 'class-powers'));
        $character->refresh();
        self::assertSame('Tremulant', $character->roles[0]->name);
        $character->delete();
    }

    public function testUpdateClass(): void
    {
        $character = PartialCharacter::create([
            'owner' => $this->user->email,
            'roles' => [
                [
                    'id' => 'banshee',
                    'level' => 1,
                    'powers' => [
                        'astrogate',
                    ],
                ],
            ],
        ]);
        session(['stillfleet-partial' => $character->id]);
        self::actingAs($this->user)
            ->postJson(
                route('stillfleet.create-class'),
                ['role' => 'tremulant'],
            )
            ->assertRedirect(route('stillfleet.create', 'class-powers'));
        $character->refresh();
        self::assertSame('Tremulant', $character->roles[0]->name);
        self::assertCount(0, $character->roles[0]->powers_additional);
        $character->delete();
    }

    public function testUpdateClassToSame(): void
    {
        $character = PartialCharacter::create([
            'owner' => $this->user->email,
            'roles' => [
                [
                    'id' => 'banshee',
                    'level' => 1,
                    'powers' => [
                        'astrogate',
                    ],
                ],
            ],
        ]);
        session(['stillfleet-partial' => $character->id]);
        self::actingAs($this->user)
            ->postJson(
                route('stillfleet.create-class'),
                ['role' => 'banshee'],
            )
            ->assertRedirect(route('stillfleet.create', 'class-powers'));
        $character->refresh();
        self::assertSame('Banshee', $character->roles[0]->name);
        self::assertCount(1, $character->roles[0]->powers_additional);
        $character->delete();
    }

    public function testCreatePowers(): void
    {
        $character = PartialCharacter::create([
            'owner' => $this->user->email,
            'roles' => [
                [
                    'id' => 'banshee',
                    'level' => 1,
                    'powers' => [],
                ],
            ],
        ]);
        session(['stillfleet-partial' => $character->id]);
        self::actingAs($this->user)
            ->get(route('stillfleet.create', 'class-powers'))
            ->assertSee('Powers');
        $character->delete();
    }
}
