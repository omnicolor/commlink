<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Tests for the skills controller for Shadowrun 5E.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class SkillsControllerTest extends TestCase
{
    /**
     * Test loading the collection of Skills if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.skills.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection of skills without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.skills.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection of skills as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.skills.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/skills/automatics',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual skills without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.skills.show', 'automatics'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid skill without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.skills.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual skill with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.skills.show', 'automatics'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'automatics',
                    'name' => 'Automatics',
                    'default' => true,
                    'group' => 'firearms',
                    'attribute' => 'agility',
                    'limit' => 'weapon',
                    'specializations' => [
                        'Assault Rifles',
                        'Cyber-Implant',
                        'Machine Pistols',
                        'Submachine Guns',
                    ],
                ],
            ]);
    }

    /**
     * Test loading an invalid skill with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.skills.show', 'not-found'))
            ->assertNotFound();
    }
}
