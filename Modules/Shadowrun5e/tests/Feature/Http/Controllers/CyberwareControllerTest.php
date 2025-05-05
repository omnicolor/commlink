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
final class CyberwareControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.cyberware.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.cyberware.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.cyberware.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.cyberware.show', 'damper'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.cyberware.show', 'damper'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.cyberware.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
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
     * Test loading an invalid resource with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.cyberware.show', 'not-found'))
            ->assertNotFound();
    }
}
