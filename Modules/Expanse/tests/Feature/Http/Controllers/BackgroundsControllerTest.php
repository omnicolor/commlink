<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;

#[Group('expanse')]
#[Medium]
final class BackgroundsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('expanse.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.backgrounds.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('expanse.backgrounds.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('expanse.backgrounds.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('expanse.backgrounds.show', 'trade'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('expanse.backgrounds.show', 'trade'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('expanse.backgrounds.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.backgrounds.show', 'trade'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'ability' => 'dexterity',
                    'benefits' => [
                        2 => ['strength' => 1],
                        3 => ['focus' => 'technology'],
                        4 => ['focus' => 'technology'],
                        5 => ['focus' => 'art'],
                        6 => ['focus' => 'tolerance'],
                        7 => ['perception' => 1],
                        8 => ['perception' => 1],
                        9 => ['grappling' => 1],
                        10 => ['focus' => 'stamina'],
                        11 => ['focus' => 'stamina'],
                        12 => ['constitution' => 1],
                    ],
                    'focuses' => [
                        'crafting',
                        'engineering',
                    ],
                    'name' => 'Trade',
                    'page' => 33,
                    'talents' => [
                        'improvisation',
                        'maker',
                    ],
                ],
            ]);
    }

    /**
     * Test loading an invalid resource with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.backgrounds.show', 'not-found'))
            ->assertNotFound();
    }
}
