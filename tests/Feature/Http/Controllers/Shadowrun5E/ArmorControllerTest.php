<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the armor controller.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 */
final class ArmorControllerTest extends \Tests\TestCase
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
            ->getJson(route('shadowrun5e.armor.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.armor.index'))
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
            ->getJson(route('shadowrun5e.armor.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/armor/armor-jacket',
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
        $this->getJson(route('shadowrun5e.armor.show', 'armor-jacket'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid modification without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.armor.show', 'not-found'))
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
            ->getJson(route('shadowrun5e.armor.show', 'armor-jacket'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'availability' => '2',
                    'cost' => 1000,
                    'id' => 'armor-jacket',
                    'name' => 'Armor Jacket',
                    'rating' => 12,
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
            ->getJson(route('shadowrun5e.armor.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
