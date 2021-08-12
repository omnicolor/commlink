<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the martial-arts-styles route for Shadowrun 5E.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class MartialArtsStylesControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-styles.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.martial-arts-styles.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection with authentication.
     * @test
     */
    public function testIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-styles.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/martial-arts-styles/aikido',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.martial-arts-styles.show', 'aikido'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource that doesn't exist.
     * @test
     */
    public function testNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.martial-arts-styles.show', 'not-found')
            )
            ->assertNotFound();
    }

    /**
     * Test loading an individual resource.
     * @test
     */
    public function testShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-styles.show', 'aikido'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'aikido',
                    'name' => 'Aikido',
                    'ruleset' => 'run-and-gun',
                    'page' => 128,
                    'techniques' => [
                        'called-shot-disarm',
                        'constrictors-crush',
                        'counterstrike',
                        'throw-person',
                        'yielding-force-counterstrike',
                        'yielding-force-throw',
                    ],
                ],
            ]);
    }
}
