<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Stillfleet;

use App\Features\Stillfleet as StillfleetFeature;
use App\Models\Stillfleet\Character;
use App\Models\Stillfleet\PartialCharacter;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Pennant\Feature;
use Tests\TestCase;

/**
 * @group controllers
 * @group stillfleet
 * @medium
 */
final class CharactersControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();
        if (!isset($this->user)) {
            $this->user = User::factory()->create();
            Feature::for($this->user)->activate(StillfleetFeature::class);
        }
    }

    public function testViewCharacter(): void
    {
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $this->user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
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
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $this->user->email,
            'system' => 'shadowrun6e',
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);
        self::actingAs($this->user)
            ->getJson(route('stillfleet.character', $character))
            ->assertNotFound();
        $character->delete();
    }

    public function testCharacterList(): void
    {
        /** @var Character */
        $character = Character::factory()->create([
            'owner' => $this->user->email,
            'created_by' => __CLASS__ . '::' . __FUNCTION__,
        ]);

        self::actingAs($this->user)
            ->get('/characters/stillfleet')
            ->assertSee($this->user->email)
            ->assertSee(e($character->name), false);

        $character->delete();
    }

    public function testCreateExplicitNew(): void
    {
        session()->put('stillfleet-partial', 'existing');
        self::actingAs($this->user)
            ->get('/characters/stillfleet/create/new')
            ->assertRedirect('/characters/stillfleet/create/class');
        self::assertNotSame('existing', session()->get('stillfleet-partial'));
    }

    public function testCreateNew(): void
    {
        PartialCharacter::where('owner', $this->user->email)
            ->delete();
        self::actingAs($this->user)
            ->get('/characters/stillfleet/create')
            ->assertOk();
        self::assertNotNull(session()->get('stillfleet-partial'));
    }

    public function testCreateChoose(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        self::actingAs($this->user)
            ->get('/characters/stillfleet/create')
            ->assertOk()
            ->assertSee('Choose character');
        self::assertNull(session()->get('stillfleet-partial'));
    }

    public function testResumeSpecific(): void
    {
        session()->flush();
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        self::actingAs($this->user)
            ->get(sprintf('/characters/stillfleet/create/%s', $character->_id))
            ->assertRedirect('/characters/stillfleet/create/class');
        self::assertSame($character->_id, session()->get('stillfleet-partial'));
    }

    public function testResumeLast(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session()->put('stillfleet-partial', $character->_id);
        self::actingAs($this->user)
            ->get('/characters/stillfleet/create')
            ->assertOk();
    }

    public function testInvalidCreationStep(): void
    {
        self::actingAs($this->user)
            ->get('/characters/stillfleet/create/unknown')
            ->assertNotFound();
    }
}
