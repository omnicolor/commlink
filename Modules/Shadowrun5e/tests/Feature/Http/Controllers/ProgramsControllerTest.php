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
final class ProgramsControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.programs.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.programs.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.programs.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.programs.show', 'armor'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.programs.show', 'armor'))
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.programs.show', 'not-found'))
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.programs.show', 'armor'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'armor',
                    'allowedDevices' => ['cyberdeck', 'rcc'],
                    'availability' => '4R',
                    'cost' => 250,
                    'effects' => [
                        'damage-resist' => 2,
                    ],
                    'name' => 'Armor',
                ],
            ]);
    }

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.programs.show', 'not-found'))
            ->assertNotFound();
    }
}
