<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;

/**
 * @group shadowrun
 * @group shadowrun5e
 */
#[Medium]
final class AdeptPowerControllerTest extends TestCase
{
    /**
     * Test loading the collection of Adept Powers if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.adept-powers.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection of Adept Powers without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.adept-powers.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection of Adept Powers as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.adept-powers.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route(
                        'shadowrun5e.adept-powers.show',
                        ['adept_power' => 'improved-sense-direction-sense'],
                    ),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual Adept Power without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route(
            'shadowrun5e.adept-powers.show',
            'improved-sense-direction-sense'
        ))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid Adept Power without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.adept-powers.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual Adept Power with authentication.
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
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
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.adept-powers.show', 'not-found'))
            ->assertNotFound();
    }
}
