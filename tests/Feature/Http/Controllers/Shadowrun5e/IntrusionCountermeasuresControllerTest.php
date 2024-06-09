<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class IntrusionCountermeasuresControllerTest extends TestCase
{
    public function testIndex(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.intrusion-countermeasures.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route(
                        'shadowrun5e.intrusion-countermeasures.show',
                        'acid',
                    ),
                ],
            ]);
    }

    public function testShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.intrusion-countermeasures.show', 'acid'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'defense' => 'Willpower + Firewall',
                    'name' => 'Acid',
                    'page' => 248,
                    'ruleset' => 'core',
                ],
            ]);
    }
}
