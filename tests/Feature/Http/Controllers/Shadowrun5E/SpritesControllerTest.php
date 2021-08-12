<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the Sprites controller for Shadowrun 5E.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class SpritesControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.sprites.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.sprites.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.sprites.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/sprites/courier',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual sprite without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.sprites.show', 'courier'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid sprite without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.sprites.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual sprite with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.sprites.show', 'courier'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'attack' => 'L',
                    'data-processing' => 'L+1',
                    'firewall' => 'L+2',
                    'id' => 'courier',
                    'initiative' => '(L*2)+1',
                    'name' => 'Courier',
                    'page' => 258,
                    'powers' => ['cookie', 'hash'],
                    'resonance' => 'L',
                    'ruleset' => 'core',
                    'skills' => ['computer', 'hacking'],
                    'sleaze' => 'L+3',
                ],
            ]);
    }

    /**
     * Test loading an invalid sprite with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.sprites.show', 'not-found'))
            ->assertNotFound();
    }
}
