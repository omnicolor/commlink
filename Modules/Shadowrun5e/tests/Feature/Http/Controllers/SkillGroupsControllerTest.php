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
final class SkillGroupsControllerTest extends TestCase
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
            ->getJson(route('shadowrun5e.skill-groups.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.skill-groups.index'))
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
            ->getJson(route('shadowrun5e.skill-groups.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/skill-groups/firearms',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual group without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.skill-groups.show', 'firearms'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid group without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.skill-groups.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual group with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.skill-groups.show', 'firearms'))
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
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.skill-groups.show', 'not-found'))
            ->assertNotFound();
    }
}
