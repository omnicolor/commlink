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
final class QualitiesControllerTest extends TestCase
{
    public function testIndexBrokenConfig(): void
    {
        Config::set('shadowrun5e.data_path', '/tmp/unused/');
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.qualities.index'))
            ->assertUnauthorized();
    }

    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('shadowrun5e.qualities.show', 'alpha-junkie'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.qualities.show', 'alpha-junkie'))
            ->assertUnauthorized();
    }

    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.qualities.show', 'not-found'))
            ->assertUnauthorized();
    }

    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.show', 'alpha-junkie'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'alpha-junkie',
                    'incompatible-with' => ['alpha-junkie'],
                    'karma' => 12,
                    'name' => 'Alpha Junkie',
                    'page' => 151,
                    'requires' => [],
                    'ruleset' => 'cutting-aces',
                ],
            ]);
    }

    /**
     * Test loading an invalid Quality with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.qualities.show', 'not-found'))
            ->assertNotFound();
    }
}
