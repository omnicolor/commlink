<?php

declare(strict_types=1);

namespace Tests\Feature\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the gear-modifications controller.
 */
final class GearModificationsControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_url', '/tmp/unused/');
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.gear-modifications.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.gear-modifications.index'))
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
            ->getJson(route('shadowrun5e.gear-modifications.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/gear-modifications/biomonitor',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual modification without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(
            route('shadowrun5e.gear-modifications.show', 'biomonitor')
        )
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid modification without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(
            route('shadowrun5e.gear-modifications.show', 'not-found')
        )
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual modification with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.gear-modifications.show', 'biomonitor')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'availability' => '3',
                    'capacity-cost' => 1,
                    'container-type' => 'commlink|cyberdeck|rcc',
                    'cost' => 300,
                    'id' => 'biomonitor',
                    'name' => 'Biomonitor',
                    'ruleset' => 'core',
                ],
            ]);
    }

    /**
     * Test loading an invalid modification with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.gear-modifications.show', 'not-found')
            )
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
