<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Http\Requests;

use App\Features\Stillfleet as StillfleetFeature;
use App\Models\User;
use Laravel\Pennant\Feature;
use Modules\Stillfleet\Models\PartialCharacter;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('stillfleet')]
#[Medium]
final class SpeciesRequestTest extends TestCase
{
    protected User $user;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        if (!isset($this->user)) {
            $this->user = User::factory()->create();
            Feature::for($this->user)->activate(StillfleetFeature::class);
        }
    }

    public function testEmptyRequest(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session(['stillfleet-partial' => $character->id]);

        self::actingAs($this->user)
            ->postJson(route('stillfleet.create-species'), [])
            ->assertInvalid([
                'species' => 'The species field is required.',
            ]);
    }

    public function testInvalidSpecies(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session(['stillfleet-partial' => $character->id]);

        self::actingAs($this->user)
            ->postJson(
                route('stillfleet.create-species'),
                ['species' => 'invalid'],
            )
            ->assertInvalid([
                'species' => 'The selected species is invalid.',
            ]);
    }
}
