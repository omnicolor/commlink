<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the programs controller for Shadowrun 5E.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class ProgramsControllerTest extends \Tests\TestCase
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
            ->getJson(route('shadowrun5e.programs.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.programs.index'))
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
            ->getJson(route('shadowrun5e.programs.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/programs/armor',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual program without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.programs.show', 'armor'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid program without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.programs.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual program with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.programs.show', 'armor'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'armor',
                    'allowedDevices' => ['cyberdeck', 'rcc'],
                    'availability' => '4R',
                    'cost' => 250,
                    'effects' => [
                        'damage-resist' => 2,
                    ],
                    'name' => 'Armor',
                ],
            ]);
    }

    /**
     * Test loading an invalid program with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.programs.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
