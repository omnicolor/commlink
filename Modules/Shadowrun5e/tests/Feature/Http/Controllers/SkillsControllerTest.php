<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;
use function route;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class SkillsControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.skills.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.skills.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.skills.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.skills.show', 'automatics'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.skills.show', 'automatics'))
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.skills.show', 'not-found'))
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
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

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.skills.show', 'not-found'))
            ->assertNotFound();
    }
}
