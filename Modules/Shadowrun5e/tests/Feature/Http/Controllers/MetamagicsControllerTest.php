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
final class MetamagicsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.shadowrun5e', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.metamagics.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.metamagics.index'))
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
            ->getJson(route('shadowrun5e.metamagics.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/metamagics/centering',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual form without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.metamagics.show', 'centering'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid form without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.metamagics.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual form with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.metamagics.show', 'centering'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'adeptOnly' => false,
                    'id' => 'centering',
                    'name' => 'Centering',
                    'page' => 325,
                    'ruleset' => 'core',
                ],
            ]);
    }

    /**
     * Test loading an invalid form with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.metamagics.show', 'not-found'))
            ->assertNotFound();
    }
}
