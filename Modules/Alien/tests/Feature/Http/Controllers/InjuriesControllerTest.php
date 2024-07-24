<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('alien')]
#[Medium]
final class InjuriesControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('alien.injuries.index'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'death_roll_modifier',
                        'effects',
                        'effects_text',
                        'fatal',
                        'healing_time',
                        'id',
                        'name',
                        'roll',
                        'time_limit',
                        'links',
                    ],
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('alien.injuries.show', 'broken-nose'))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'death_roll_modifier',
                    'effects',
                    'effects_text',
                    'fatal',
                    'healing_time',
                    'id',
                    'name',
                    'roll',
                    'time_limit',
                    'links',
                ],
            ]);
    }
}
