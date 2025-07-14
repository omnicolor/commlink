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
final class AttributesRequestTest extends TestCase
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
            ->postJson(route('stillfleet.create-attributes'), [])
            ->assertInvalid([
                'dice-option' => ['The dice-option field is required.'],
                'CHA' => ['The charm field is required.'],
                'COM' => [
                    'The combat field is required.',
                    'It looks like you have sent invalid dice choices.',
                ],
                'MOV' => ['The movement field is required.'],
                'REA' => ['The reason field is required.'],
                'WIL' => ['The will field is required.'],
            ]);
    }

    public function testInvalidDiceChoices(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session(['stillfleet-partial' => $character->id]);

        self::actingAs($this->user)
            ->postJson(
                route('stillfleet.create-attributes'),
                [
                    'dice-option' => 'option1',
                    'CHA' => 'd12',
                    'COM' => 'd10',
                    'MOV' => 'd8',
                    'REA' => 'd8', // Too many D8s.
                    'WIL' => 'd6',
                ],
            )
            ->assertInvalid(['COM' => 'It looks like you have sent invalid dice choices.']);
    }

    public function testValid(): void
    {
        $character = PartialCharacter::create(['owner' => $this->user->email]);
        session(['stillfleet-partial' => $character->id]);

        self::actingAs($this->user)
            ->postJson(
                route('stillfleet.create-attributes'),
                [
                    'dice-option' => 'option1',
                    'CHA' => 'd6',
                    'COM' => 'd10',
                    'MOV' => 'd8',
                    'REA' => 'd6',
                    'WIL' => 'd12',
                ],
            )
            ->assertRedirect(route('stillfleet.create', 'gear'));
    }
}
