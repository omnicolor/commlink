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
final class MartialArtsTechniquesControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertUnauthorized();
    }

    public function testIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.martial-arts-techniques.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.martial-arts-techniques.show', 'called-shot-disarm'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(route(
            'shadowrun5e.martial-arts-techniques.show',
            'called-shot-disarm'
        ))
            ->assertUnauthorized();
    }

    public function testNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(
                route('shadowrun5e.martial-arts-techniques.show', 'not-found')
            )
            ->assertNotFound();
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route(
                'shadowrun5e.martial-arts-techniques.show',
                'called-shot-disarm'
            ))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'called-shot-disarm',
                    'name' => 'Called Shot',
                    'page' => 111,
                    'ruleset' => 'run-and-gun',
                    'subname' => 'Disarm',
                ],
            ]);
    }
}
