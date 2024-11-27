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
final class TraditionsControllerTest extends TestCase
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
            ->getJson(route('shadowrun5e.traditions.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.traditions.index'))
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
            ->getJson(route('shadowrun5e.traditions.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/traditions/norse',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual tradition without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.traditions.show', 'norse'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid tradition without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.traditions.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual tradition with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.traditions.show', 'norse'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'drain' => 'Willpower + Logic',
                    'elements' => [
                        'combat' => 'Guardian',
                        'detection' => 'Earth',
                        'health' => 'Plant',
                        'illusion' => 'Air',
                        'manipulation' => 'Fire',
                    ],
                    'id' => 'norse',
                    'name' => 'Norse',
                    'page' => 4,
                    'ruleset' => 'shadow-spells',
                ],
            ]);
    }

    /**
     * Test loading an invalid tradition with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.traditions.show', 'not-found'))
            ->assertNotFound();
    }
}
