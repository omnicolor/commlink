<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('stillfleet')]
#[Medium]
final class ArmorControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('stillfleet.armor.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'cost',
                        'damage_reduction',
                        'id',
                        'name',
                        'notes',
                        'page',
                        'ruleset',
                        'tech_cost',
                        'tech_strata',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('stillfleet.armor.show', 'chainmail'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'cost',
                    'damage_reduction',
                    'id',
                    'name',
                    'notes',
                    'page',
                    'ruleset',
                    'tech_cost',
                    'tech_strata',
                    'links',
                ],
            ]);
    }
}
