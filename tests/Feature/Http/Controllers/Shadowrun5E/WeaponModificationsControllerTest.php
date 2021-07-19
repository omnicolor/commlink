<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5E;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the weapon modifications controller.
 * @group controllers
 * @group shadowrun
 * @group shadowrun5e
 * @medium
 */
final class WeaponModificationsControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.weapon-modifications.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.weapon-modifications.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
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
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual modification without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(
            route('shadowrun5e.weapon-modifications.show', 'bayonet')
        )
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid modification without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(
            route('shadowrun5e.weapon-modifications.show', 'not-found')
        )
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual modification with authentication.
     * @test
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
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.weapon-modifications.show', 'not-found')
            )
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
