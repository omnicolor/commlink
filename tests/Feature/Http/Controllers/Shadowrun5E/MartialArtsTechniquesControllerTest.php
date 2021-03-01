<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the martial-arts-techniques route for Shadowrun 5E.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 */
final class MartialArtsTechniquesControllerTest extends \Tests\TestCase
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
            ->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading the collection with authentication.
     * @test
     */
    public function testIndex(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/martial-arts-techniques/called-shot-disarm',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route(
            'shadowrun5e.martial-arts-techniques.show',
            'called-shot-disarm'
        ))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual resource that doesn't exist.
     * @test
     */
    public function testNotFound(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.martial-arts-techniques.show', 'not-found')
            )
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Test loading an individual resource.
     * @test
     */
    public function testShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route(
                'shadowrun5e.martial-arts-techniques.show',
                'called-shot-disarm'
            ))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'called-shot-disarm',
                    'name' => 'Called Shot',
                    'page' => 111,
                    'ruleset' => 'run-and-gun',
                    'subname' => 'Disarm',
                ],
            ]);
    }
}
