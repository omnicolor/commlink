<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Tests\TestCase;

/**
 * Tests for the GruntsController.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class GruntsControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.grunts.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.grunts.show', 'pr-0'),
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.grunts.show', 'pr-0'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'initiative_base' => 6,
                    'initiative_dice' => 1,
                    'intuition' => 3,
                    'logic' => 2,
                    'name' => 'Thugs & mouth breathers',
                ],
            ]);
    }
}
