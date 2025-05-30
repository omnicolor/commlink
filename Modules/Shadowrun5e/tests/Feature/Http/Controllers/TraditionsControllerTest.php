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
use function route;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class TraditionsControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.traditions.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.traditions.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.traditions.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.traditions.show', 'norse'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.traditions.show', 'norse'))
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.traditions.show', 'not-found'))
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
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

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.traditions.show', 'not-found'))
            ->assertNotFound();
    }
}
