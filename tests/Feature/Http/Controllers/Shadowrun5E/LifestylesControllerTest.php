<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the lifestyles controller.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class LifestylesControllerTest extends \Tests\TestCase
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
            ->getJson(route('shadowrun5e.lifestyles.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.lifestyles.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
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
            ->getJson(route('shadowrun5e.lifestyles.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/lifestyles/low',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(
            route('shadowrun5e.lifestyles.show', 'low')
        )
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid resource without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.lifestyles.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual resource with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.lifestyles.show', 'low')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'comforts' => 2,
                        'comfortsMax' => 3,
                        'neighborhood' => 2,
                        'neighborhoodMax' => 3,
                        'security' => 2,
                        'securityMax' => 3,
                    ],
                    'cost' => 2000,
                    'id' => 'low',
                    'name' => 'Low',
                    'page' => 373,
                    'points' => 3,
                    'ruleset' => 'core',
                ],
            ]);
    }

    /**
     * Test loading an invalid resource with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.lifestyles.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
