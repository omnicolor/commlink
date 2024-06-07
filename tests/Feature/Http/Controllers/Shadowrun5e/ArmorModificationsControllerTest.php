<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

use function count;

/**
 * Tests for the armor-modifications controller.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class ArmorModificationsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.armor-modifications.index'))
            ->assertInternalServerError();
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.armor-modifications.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.armor-modifications.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.armor-modifications.show', 'auto-injector'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual modification without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(
            route('shadowrun5e.armor-modifications.show', 'auto-injector')
        )
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid modification without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(
            route('shadowrun5e.armor-modifications.show', 'not-found')
        )
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual modification with authentication.
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.armor-modifications.show', 'auto-injector')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'availability' => '4',
                    'capacity_cost' => 2,
                    'cost' => 1500,
                    'id' => 'auto-injector',
                    'name' => 'Auto-injector',
                    'ruleset' => 'run-and-gun',
                ],
            ]);
    }

    /**
     * Test loading an invalid modification with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.armor-modifications.show', 'not-found')
            )
            ->assertNotFound();
    }

    public function testCleansWirelessEffects(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.armor-modifications.show', 'argentum-coat')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'wireless_effects' => [
                        'social-tests' => 1,
                    ],
                ],
            ]);
    }
}
