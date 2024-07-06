<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class LifestylesControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.lifestyles.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.lifestyles.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.lifestyles.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/lifestyles/low',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(
            route('shadowrun5e.lifestyles.show', 'low')
        )
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.lifestyles.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.lifestyles.show', 'low')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'attributes' => [
                        'comforts' => 2,
                        'comfortsMax' => 3,
                        'neighborhood' => 2,
                        'neighborhoodMax' => 3,
                        'security' => 2,
                        'securityMax' => 3,
                    ],
                    'cost' => 2000,
                    'id' => 'low',
                    'name' => 'Low',
                    'page' => 373,
                    'points' => 3,
                    'ruleset' => 'core',
                ],
            ]);
    }

    /**
     * Test loading an invalid resource with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.lifestyles.show', 'not-found'))
            ->assertNotFound();
    }
}
