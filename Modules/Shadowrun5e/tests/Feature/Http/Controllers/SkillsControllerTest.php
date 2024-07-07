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
final class SkillsControllerTest extends TestCase
{
    /**
     * Test loading the collection of Skills if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.skills.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection of skills without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.skills.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection of skills as an authenticated user.
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
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.skills.show', 'automatics'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid skill without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.skills.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual skill with authentication.
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
