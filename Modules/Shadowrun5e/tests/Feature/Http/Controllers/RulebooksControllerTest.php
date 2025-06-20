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
