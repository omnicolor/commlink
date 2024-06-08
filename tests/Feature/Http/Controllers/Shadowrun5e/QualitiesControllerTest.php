<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

/**
 * @group shadowrun
 * @group shadowrun5e
 */
#[Medium]
final class QualitiesControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.qualities.index'))
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
            ->getJson(route('shadowrun5e.qualities.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/qualities/alpha-junkie',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual Quality without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.qualities.show', 'alpha-junkie'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid quality without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.qualities.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual Quality with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
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
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.show', 'not-found'))
            ->assertNotFound();
    }
}
