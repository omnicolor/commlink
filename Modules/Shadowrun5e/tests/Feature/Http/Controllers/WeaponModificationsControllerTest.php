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
final class WeaponModificationsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.weapon-modifications.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.weapon-modifications.index'))
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
            ->getJson(route('shadowrun5e.weapon-modifications.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/weapon-modifications/bayonet',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual modification without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(
            route('shadowrun5e.weapon-modifications.show', 'bayonet')
        )
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid modification without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(
            route('shadowrun5e.weapon-modifications.show', 'not-found')
        )
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual modification with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.weapon-modifications.show', 'bayonet'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'availability' => '4R',
                    'cost' => 50,
                    'id' => 'bayonet',
                    'mount' => ['top', 'under'],
                    'name' => 'Bayonet',
                    'ruleset' => 'run-and-gun',
                    'type' => 'accessory',
                ],
            ]);
    }

    /**
     * Test loading an invalid modification with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.weapon-modifications.show', 'not-found')
            )
            ->assertNotFound();
    }
}
