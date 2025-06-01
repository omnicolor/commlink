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
final class MartialArtsStylesControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-styles.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.martial-arts-styles.index'))
            ->assertUnauthorized();
    }

    public function testIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-styles.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.martial-arts-styles.show', 'aikido'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.martial-arts-styles.show', 'aikido'))
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.martial-arts-styles.show', 'not-found')
            )
            ->assertNotFound();
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-styles.show', 'aikido'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'aikido',
                    'name' => 'Aikido',
                    'ruleset' => 'run-and-gun',
                    'page' => 128,
                    'techniques' => [
                        'called-shot-disarm',
                        'constrictors-crush',
                        'counterstrike',
                        'throw-person',
                        'yielding-force-counterstrike',
                        'yielding-force-throw',
                    ],
                ],
            ]);
    }
}
