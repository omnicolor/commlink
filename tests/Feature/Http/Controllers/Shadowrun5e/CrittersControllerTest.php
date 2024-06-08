<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * Tests for the CrittersController.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Medium]
final class CrittersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.critters.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.critters.show', 'barghest'),
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.critters.show', 'barghest'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'agility' => 5,
                    'armor' => 3,
                    'body' => 8,
                    'charisma' => 5,
                    'condition_physical' => 12,
                    'condition_stun' => 10,
                ],
            ]);
    }
}
