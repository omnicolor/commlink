<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;

#[Group('expanse')]
#[Medium]
final class FocusesControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('expanse.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.focuses.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('expanse.focuses.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('expanse.focuses.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route(
                        'expanse.focuses.show',
                        ['focus' => 'crafting'],
                    ),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('expanse.focuses.show', 'crafting'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('expanse.focuses.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.focuses.show', 'crafting'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'attribute' => 'dexterity',
                    'name' => 'Crafting',
                    'page' => 47,
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
            ->getJson(route('expanse.focuses.show', 'not-found'))
            ->assertNotFound();
    }
}
