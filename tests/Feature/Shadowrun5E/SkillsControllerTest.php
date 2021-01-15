<?php

declare(strict_types=1);

namespace Tests\Feature\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

final class SkillsControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection of Skills if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_url', '/tmp/unused/');
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
        $response = $this->getJson(route('shadowrun5e.skills.index'))
            ->assertOk();
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading the collection of skills as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.skills.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/skills/automatics',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual skills without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.skills.show', 'automatics'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'automatics',
                    'name' => 'Automatics',
                    'default' => true,
                    'group' => 'firearms',
                    'attribute' => 'agility',
                    'description' => 'Skill description here.',
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
     * Test loading an invalid skill without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.skills.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Test loading an individual skill with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->getJson(route(
            'shadowrun5e.skills.show',
            'automatics'
        ))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'automatics',
                    'name' => 'Automatics',
                    'default' => true,
                    'group' => 'firearms',
                    'attribute' => 'agility',
                    'description' => 'Skill description here.',
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
        $user = User::factory()->create();
        $this->getJson(route(
            'shadowrun5e.skills.show',
            'not-found'
        ))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
