<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function route;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class ResonanceEchoesControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.resonance-echoes.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route(
                        'shadowrun5e.resonance-echoes.show',
                        'attack-upgrade',
                    ),
                ],
            ]);
    }

    public function testShowNotFound(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.resonance-echoes.show', 'not-found'))
            ->assertNotFound();
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route(
                'shadowrun5e.resonance-echoes.show',
                'attack-upgrade'
            ))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'description' => 'Description of Attack upgrade echo.',
                    'effects' => [
                        'attack' => 1,
                    ],
                    'id' => 'attack-upgrade',
                    'limit' => 2,
                    'name' => 'Attack upgrade',
                    'page' => 258,
                    'ruleset' => 'core',
                ],
            ]);
    }
}
