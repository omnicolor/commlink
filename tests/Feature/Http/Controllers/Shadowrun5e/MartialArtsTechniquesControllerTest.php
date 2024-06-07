<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Tests for the martial-arts-techniques route for Shadowrun 5E.
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class MartialArtsTechniquesControllerTest extends TestCase
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
            ->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection with authentication.
     */
    public function testIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/martial-arts-techniques/called-shot-disarm',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route(
            'shadowrun5e.martial-arts-techniques.show',
            'called-shot-disarm'
        ))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource that doesn't exist.
     */
    public function testNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.martial-arts-techniques.show', 'not-found')
            )
            ->assertNotFound();
    }

    /**
     * Test loading an individual resource.
     */
    public function testShow(): void
    {
        /** @var User */
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
