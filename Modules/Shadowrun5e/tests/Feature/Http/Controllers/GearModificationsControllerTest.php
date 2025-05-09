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
use function route;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class GearModificationsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.gear-modifications.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.gear-modifications.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.gear-modifications.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.gear-modifications.show', 'biomonitor'),
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
            route('shadowrun5e.gear-modifications.show', 'biomonitor')
        )
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid modification without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(
            route('shadowrun5e.gear-modifications.show', 'not-found')
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
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.gear-modifications.show', 'not-found')
            )
            ->assertNotFound();
    }
}
