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
final class ClassPowersRequestTest extends TestCase
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
        $character = PartialCharacter::create([
            'owner' => $this->user->email,
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);
        session(['stillfleet-partial' => $character->id]);

        self::actingAs($this->user)
            ->postJson(route('stillfleet.create-class-powers'), [])
            ->assertInvalid([
                'powers' => 'The power(s) field is required.',
            ]);
    }

    public function testTooManyPowers(): void
    {
        $character = PartialCharacter::create([
            'owner' => $this->user->email,
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);
        session(['stillfleet-partial' => $character->id]);

        self::actingAs($this->user)
            ->postJson(
                route('stillfleet.create-class-powers'),
                [
                    'powers' => ['astrogate', 'interface'],
                ],
            )
            ->assertInvalid([
                'powers' => 'You may not add more than 1 power(s).',
            ]);
    }
}
