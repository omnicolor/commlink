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
