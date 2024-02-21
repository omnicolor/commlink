<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

use function count;

/**
 * Tests for the ammunition controller.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class AmmunitionControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.ammunition.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.ammunition.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.ammunition.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.ammunition.show', 'apds'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual ammunition without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.ammunition.show', 'apds'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid ammunition without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.ammunition.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual ammunition with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.ammunition.show', 'apds'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'apds',
                    'ap_modifier' => -4,
                    'availability' => '12F',
                    'cost' => 120,
                    'name' => 'APDS',
                ],
            ]);
    }

    /**
     * Test loading an invalid ammunition with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.ammunition.show', 'not-found'))
            ->assertNotFound();
    }
}
