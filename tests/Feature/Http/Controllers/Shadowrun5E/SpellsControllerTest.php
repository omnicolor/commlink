<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the Spells controller for Shadowrun5e.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class SpellsControllerTest extends \Tests\TestCase
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
            ->getJson(route('shadowrun5e.spells.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.spells.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.spells.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/spells/control-emotions',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual spell with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.spells.show', 'control-emotions'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'category' => 'Manipulation',
                    'drain' => 'F-1',
                    'duration' => 'S',
                    'id' => 'control-emotions',
                    'name' => 'Control Emotions',
                    'page' => 21,
                    'range' => 'LOS',
                    'ruleset' => 'shadow-spells',
                    'tags' => ['mental'],
                    'type' => 'M',
                ],
            ]);
    }

    /**
     * Test loading an invalid spell with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.spells.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
