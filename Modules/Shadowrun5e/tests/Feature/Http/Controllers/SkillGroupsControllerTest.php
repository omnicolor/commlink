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

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class SkillGroupsControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.skill-groups.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.skill-groups.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $response = self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.skill-groups.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'collection' => route('shadowrun5e.skill-groups.index'),
                    'system' => '/api/shadowrun5e',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.skill-groups.show', 'firearms'))
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.skill-groups.show', 'not-found'))
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.skill-groups.show', 'firearms'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'skills' => [],
                    'id' => 'firearms',
                    'links' => [
                        'self' => route('shadowrun5e.skill-groups.show', 'firearms'),
                    ],
                ],
            ]);
    }

    public function testAuthShowNotFound(): void
    {
        self::actingAs(User::factory()->create())
            ->getJson(route('shadowrun5e.skill-groups.show', 'not-found'))
            ->assertNotFound();
    }
}
