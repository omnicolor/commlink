<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Expanse;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the backgrounds controller.
 * @covers \App\Http\Controllers\Expanse\BackgroundsController
 * @group controllers
 * @group expanse
 */
final class BackgroundsControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_path.expanse', '/tmp/unused/');
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('expanse.backgrounds.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('expanse.backgrounds.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('expanse.backgrounds.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/expanse/backgrounds/trade',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('expanse.backgrounds.show', 'trade'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid resource without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('expanse.backgrounds.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual resource with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('expanse.backgrounds.show', 'trade'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'ability' => 'dexterity',
                    'benefits' => [
                        2 => ['strength' => 1],
                        3 => ['focus' => 'technology'],
                        4 => ['focus' => 'technology'],
                        5 => ['focus' => 'art'],
                        6 => ['focus' => 'tolerance'],
                        7 => ['perception' => 1],
                        8 => ['perception' => 1],
                        9 => ['grappling' => 1],
                        10 => ['focus' => 'stamina'],
                        11 => ['focus' => 'stamina'],
                        12 => ['constitution' => 1],
                    ],
                    'focuses' => [
                        'crafting',
                        'engineering',
                    ],
                    'name' => 'Trade',
                    'page' => 33,
                    'talents' => [
                        'improvisation',
                        'maker',
                    ],
                ],
            ]);
    }

    /**
     * Test loading an invalid resource with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('expanse.backgrounds.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
