<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class AmmunitionControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.ammunition.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.ammunition.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
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
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.ammunition.show', 'apds'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid ammunition without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.ammunition.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual ammunition with authentication.
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
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.ammunition.show', 'not-found'))
            ->assertNotFound();
    }

    public function testCleansDamageModifier(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.ammunition.show', 'depleted-uranium'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'damage_modifier' => '+1',
                ],
            ]);
    }
}
