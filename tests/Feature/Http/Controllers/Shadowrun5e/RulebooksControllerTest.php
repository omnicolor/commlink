<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Tests\TestCase;

/**
 * Tests for the RulebooksController.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class RulebooksControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.rulebooks.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.rulebooks.show', 'core'),
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.rulebooks.show', 'core'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'name' => 'Core 5th Edition',
                    'required' => true,
                ],
            ]);
    }
}
