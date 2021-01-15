<?php

declare(strict_types=1);

namespace Tests\Feature\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

final class SkillGroupsControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_url', '/tmp/unused/');
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.skill-groups.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $response = $this->getJson(route('shadowrun5e.skill-groups.index'))
            ->assertOk();
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.skill-groups.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/skill-groups/firearms',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual group without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.skill-groups.show', 'firearms'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'skills' => [],
                    'id' => 'firearms',
                    'links' => [
                        'self' => '/api/shadowrun5e/skill-groups/firearms',
                    ],
                ],
            ]);
    }

    /**
     * Test loading an invalid group without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.skill-groups.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }

    /**
     * Test loading an individual group with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->getJson(route('shadowrun5e.skill-groups.show', 'firearms'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'skills' => [],
                    'id' => 'firearms',
                    'links' => [
                        'self' => '/api/shadowrun5e/skill-groups/firearms',
                    ],
                ],
            ]);
    }

    /**
     * Test loading an invalid group with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        $this->getJson(route('shadowrun5e.skill-groups.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
