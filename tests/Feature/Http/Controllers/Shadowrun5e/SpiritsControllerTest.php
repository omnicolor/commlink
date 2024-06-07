<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Tests for the Spirits controller for Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class SpiritsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.spirits.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.spirits.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.spirits.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/spirits/air',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual spirit with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
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

    /**
     * Test loading an invalid spirit with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.spirits.show', 'not-found'))
            ->assertNotFound();
    }
}
