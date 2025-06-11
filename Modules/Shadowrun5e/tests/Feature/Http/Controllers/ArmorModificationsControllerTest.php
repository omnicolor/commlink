<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;
use function route;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class ArmorModificationsControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.armor-modifications.index'))
            ->assertInternalServerError();
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.armor-modifications.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.armor-modifications.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.armor-modifications.show', 'auto-injector'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(
            route('shadowrun5e.armor-modifications.show', 'auto-injector')
        )
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(
            route('shadowrun5e.armor-modifications.show', 'not-found')
        )
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.armor-modifications.show', 'auto-injector')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'availability' => '4',
                    'capacity_cost' => 2,
                    'cost' => 1500,
                    'id' => 'auto-injector',
                    'name' => 'Auto-injector',
                    'ruleset' => 'run-and-gun',
                ],
            ]);
    }

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.armor-modifications.show', 'not-found')
            )
            ->assertNotFound();
    }

    public function testCleansWirelessEffects(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.armor-modifications.show', 'argentum-coat')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'wireless_effects' => [
                        'social-tests' => 1,
                    ],
                ],
            ]);
    }
}
