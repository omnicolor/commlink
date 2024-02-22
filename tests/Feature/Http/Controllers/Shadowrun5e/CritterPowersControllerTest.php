<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Tests\TestCase;

/**
 * Tests for the CritterPowersController.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class CritterPowersControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.critter-powers.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.critter-powers.show', 'fear'),
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.critter-powers.show', 'fear'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'action' => 'Complex',
                    'duration' => 'Special',
                    'name' => 'Fear',
                    'page' => 397,
                    'range' => 'LOS',
                    'ruleset' => 'core',
                    'type' => 'M',
                ],
            ]);
    }
}
