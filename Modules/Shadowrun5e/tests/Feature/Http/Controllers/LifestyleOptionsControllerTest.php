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
final class LifestyleOptionsControllerTest extends TestCase
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
            ->getJson(route('shadowrun5e.lifestyle-options.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.lifestyle-options.index'))
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
            ->getJson(route('shadowrun5e.lifestyle-options.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/lifestyle-options/swimming-pool',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(
            route('shadowrun5e.lifestyle-options.show', 'swimming-pool')
        )
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.lifestyle-options.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(
                route('shadowrun5e.lifestyle-options.show', 'swimming-pool')
            )
            ->assertOk()
            ->assertJson([
                'data' => [
                    'cost' => 100,
                    'id' => 'swimming-pool',
                    'minimumLifestyle' => 'Middle',
                    'name' => 'Swimming Pool',
                    'page' => 224,
                    'points' => 1,
                    'ruleset' => 'run-faster',
                    'type' => 'Asset',
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
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.lifestyle-options.show', 'not-found'))
            ->assertNotFound();
    }
}
