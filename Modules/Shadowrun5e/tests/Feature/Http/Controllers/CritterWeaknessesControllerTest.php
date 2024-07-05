<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class CritterWeaknessesControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.critter-weaknesses.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.critter-weaknesses.show', 'allergy'),
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.critter-weaknesses.show', 'allergy'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'allergy',
                    'name' => 'Allergy',
                    'page' => 401,
                    'ruleset' => 'core',
                ],
            ]);
    }
}
