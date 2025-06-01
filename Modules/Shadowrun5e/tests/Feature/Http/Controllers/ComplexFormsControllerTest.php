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
final class ComplexFormsControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.complex-forms.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        self::getJson(route('shadowrun5e.complex-forms.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('shadowrun5e.complex-forms.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.complex-forms.show', 'cleaner'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        self::getJson(route('shadowrun5e.complex-forms.show', 'cleaner'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('shadowrun5e.complex-forms.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.complex-forms.show', 'cleaner'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'duration' => 'P',
                    'fade' => 'L+1',
                    'id' => 'cleaner',
                    'name' => 'Cleaner',
                    'page' => 252,
                    'ruleset' => 'core',
                    'target' => 'Persona',
                ],
            ]);
    }

    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('shadowrun5e.complex-forms.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
