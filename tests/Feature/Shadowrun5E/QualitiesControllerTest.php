<?php

declare(strict_types=1);

namespace Tests\Feature\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the qualities controller.
 */
final class QualitiesControllerTest extends \Tests\TestCase
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
            ->getJson(route('shadowrun5e.qualities.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $response = $this->getJson(route('shadowrun5e.qualities.index'))
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
            ->getJson(route('shadowrun5e.qualities.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/qualities/alpha-junkie',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual Quality without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.qualities.show', 'alpha-junkie'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid quality without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.qualities.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual Quality with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.show', 'alpha-junkie'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'alpha-junkie',
                    'incompatible-with' => ['alpha-junkie'],
                    'karma' => 12,
                    'name' => 'Alpha Junkie',
                    'page' => 151,
                    'requires' => [],
                    'ruleset' => 'cutting-aces',
                ],
            ]);
    }

    /**
     * Test loading an invalid Quality with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
