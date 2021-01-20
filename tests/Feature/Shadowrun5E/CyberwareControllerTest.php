<?php

declare(strict_types=1);

namespace Tests\Feature\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the Cyberware controller.
 */
final class CyberwareControllerTest extends \Tests\TestCase
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
            ->getJson(route('shadowrun5e.cyberware.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $response = $this->getJson(route('shadowrun5e.cyberware.index'))
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
            ->getJson(route('shadowrun5e.cyberware.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/cyberware/damper',
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
        $this->getJson(route('shadowrun5e.cyberware.show', 'damper'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid form without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.cyberware.show', 'not-found'))
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
            ->getJson(route('shadowrun5e.cyberware.show', 'image-link'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'image-link',
                    'availability' => '4',
                    'capacity-containers' => [
                        'cybereyes-1',
                        'cybereyes-2',
                        'cybereyes-3',
                        'cybereyes-4',
                    ],
                    'capacity-cost' => 0,
                    'cost' => 1000,
                    'description' => 'Image link description.',
                    'effects' => [],
                    'essence' => 0.1,
                    'incompatibilities' => [],
                    'name' => 'Image Link',
                    'ruleset' => 'core',
                    'type' => 'cyberware',
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
            ->getJson(route('shadowrun5e.cyberware.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
