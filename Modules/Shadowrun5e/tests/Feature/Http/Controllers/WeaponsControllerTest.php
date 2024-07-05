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
final class WeaponsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.weapons.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.weapons.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.weapons.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.weapons.show', 'ak-98'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual weapon without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.weapons.show', 'ak-98'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid weapon without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.weapons.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual weapon with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.weapons.show', 'ak-98'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'ak-98',
                    'accuracy' => 5,
                    'ammo-capacity' => 38,
                    'ammo-container' => 'c',
                    'armor-piercing' => -2,
                    'availability' => '8F',
                    'class' => 'Assault Rifle',
                    'cost' => 1250,
                    'damage' => '10P',
                    'modes' => ['SA', 'BF', 'FA'],
                    'mounts' => ['top', 'barrel', 'stock'],
                    'name' => 'AK-98',
                    'range' => '25/150/350/550',
                    'ruleset' => 'run-and-gun',
                    'skill' => 'automatics',
                    'type' => 'firearm',
                ],
            ]);
    }

    /**
     * Test loading an invalid weapon with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.weapons.show', 'not-found'))
            ->assertNotFound();
    }
}
