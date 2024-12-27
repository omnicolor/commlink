<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Http\Controllers;

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

use function count;

#[Group('expanse')]
#[Medium]
final class FocusesControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);
        $this->app->make(PermissionRegistrar::class)
            ->forgetCachedPermissions();
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('expanse.focuses.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $response = self::actingAs(User::factory()->create())
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

    public function testNoAuthShow(): void
    {
        self::getJson(route('expanse.focuses.show', 'crafting'))
            ->assertUnauthorized();
    }

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
        self::actingAs(User::factory()->create())
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
        self::actingAs(User::factory()->create())
            ->getJson(route('expanse.focuses.show', 'not-found'))
            ->assertNotFound();
    }
}
