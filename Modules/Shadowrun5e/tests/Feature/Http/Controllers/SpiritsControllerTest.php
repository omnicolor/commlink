<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;
use function route;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class SpiritsControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.spirits.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.spirits.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.spirits.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.spirits.show', 'air'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.spirits.show', 'air'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'agility' => 'F+3',
                    'body' => 'F-2',
                    'charisma' => 'F',
                    'edge' => 'F/2',
                    'essence' => 'F',
                    'id' => 'air',
                    'initiative-astral' => '(F*2)+3d6',
                    'initiative' => '(F*2+4)+2d6',
                    'intuition' => 'F',
                    'logic' => 'F',
                    'name' => 'Spirit of Air',
                    'magic' => 'F',
                    'page' => 303,
                    'powers' => [
                        'accident',
                        'astral-form',
                        'concealment',
                        'confusion',
                        'engulf',
                        'materialization',
                        'movement',
                        'sapience',
                        'search',
                    ],
                    'powers-optional' => [
                        'elemental-attack',
                        'energy-aura',
                        'fear',
                        'guard',
                        'noxious-breath',
                        'psychokinesis',
                    ],
                    'reaction' => 'F+4',
                    'ruleset' => 'core',
                    'skills' => [
                        'assensing',
                        'astral-combat',
                        'exotic-ranged-weapon',
                        'perception',
                        'running',
                        'unarmed-combat',
                    ],
                    'special' => 'Spirits of Air get +10 meters per hit when Sprinting',
                    'strength' => 'F-3',
                    'willpower' => 'F',
                ],
            ]);
    }

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.spirits.show', 'not-found'))
            ->assertNotFound();
    }
}
