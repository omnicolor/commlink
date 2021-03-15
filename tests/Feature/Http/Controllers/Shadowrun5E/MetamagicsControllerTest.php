<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the Metamagics controller.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 */
final class MetamagicsControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.metamagics.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.metamagics.index'))
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
            ->getJson(route('shadowrun5e.metamagics.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/metamagics/centering',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual form without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.metamagics.show', 'centering'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid form without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.metamagics.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual form with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.metamagics.show', 'centering'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'adeptOnly' => false,
                    'id' => 'centering',
                    'name' => 'Centering',
                    'page' => 325,
                    'ruleset' => 'core',
                ],
            ]);
    }

    /**
     * Test loading an invalid form with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.metamagics.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
