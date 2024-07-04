<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class GearControllerTest extends TestCase
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
            ->getJson(route('shadowrun5e.gear.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.gear.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->seed();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.gear.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/gear/credstick-gold',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.gear.show', 'credstick-gold'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.gear.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->seed();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.gear.show', 'credstick-gold'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'credstick-gold',
                    'availability' => '5',
                    'cost' => 100,
                    'name' => 'Certified Credstick',
                    'subname' => 'Gold',
                ],
            ]);
    }

    /**
     * Test loading an invalid resource with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.gear.show', 'not-found'))
            ->assertNotFound();
    }
}
