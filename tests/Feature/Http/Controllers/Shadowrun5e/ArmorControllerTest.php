<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function count;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class ArmorControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.armor.index'))
            ->assertInternalServerError();
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.armor.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.armor.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.armor.show', 'armor-jacket'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual modification without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.armor.show', 'armor-jacket'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid modification without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.armor.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual modification with authentication.
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.armor.show', 'armor-jacket'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'availability' => '2',
                    'cost' => 1000,
                    'id' => 'armor-jacket',
                    'name' => 'Armor Jacket',
                    'rating' => 12,
                ],
            ]);
    }

    /**
     * Test loading an invalid modification with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.armor.show', 'not-found'))
            ->assertNotFound();
    }
}
