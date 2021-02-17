<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the AdeptPowersController.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 */
final class AdeptPowerControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection of Adept Powers if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_url', '/tmp/unused/');
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.adept-powers.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection of Adept Powers without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.adept-powers.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading the collection of Adept Powers as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.adept-powers.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/adept-powers/improved-sense-direction-sense',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual Adept Power without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route(
            'shadowrun5e.adept-powers.show',
            'improved-sense-direction-sense'
        ))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid Adept Power without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.adept-powers.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual Adept Power with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route(
                'shadowrun5e.adept-powers.show',
                'improved-sense-direction-sense'
            ))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'cost' => 0.25,
                    'id' => 'improved-sense-direction-sense',
                    'name' => 'Improved Sense: Direction Sense',
                    'page' => '310',
                    'ruleset' => 'core',
                ],
            ]);
    }

    /**
     * Test loading an invalid Adept Power with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.adept-powers.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
