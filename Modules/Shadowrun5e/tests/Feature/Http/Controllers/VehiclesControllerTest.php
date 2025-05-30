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
final class VehiclesControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.vehicles.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.vehicles.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.vehicles.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.vehicles.show', 'dodge-scoot'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.vehicles.show', 'dodge-scoot'))
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.vehicles.show', 'not-found'))
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.vehicles.show', 'dodge-scoot'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'acceleration' => 1,
                    'armor' => 4,
                    'availability' => '',
                    'body' => 4,
                    'category' => 'bike',
                    'cost' => 3000,
                    'deviceRating' => 1,
                    'handling' => 4,
                    'handlingOffRoad' => 3,
                    'id' => 'dodge-scoot',
                    'name' => 'Dodge Scoot',
                    'pilot' => 1,
                    'seats' => 1,
                    'sensor' => 1,
                    'speed' => 3,
                    'type' => 'groundcraft',
                ],
            ]);
    }

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.vehicles.show', 'not-found'))
            ->assertNotFound();
    }
}
