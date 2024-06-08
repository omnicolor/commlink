<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Shadowrun5e;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Medium]
final class SpritesControllerTest extends TestCase
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
            ->getJson(route('shadowrun5e.sprites.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('shadowrun5e.sprites.index'))
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
            ->getJson(route('shadowrun5e.sprites.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/shadowrun5e/sprites/courier',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual sprite without authentication.
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('shadowrun5e.sprites.show', 'courier'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid sprite without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('shadowrun5e.sprites.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual sprite with authentication.
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.sprites.show', 'courier'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'attack' => 'L',
                    'data-processing' => 'L+1',
                    'firewall' => 'L+2',
                    'id' => 'courier',
                    'initiative' => '(L*2)+1',
                    'name' => 'Courier',
                    'page' => 258,
                    'powers' => ['cookie', 'hash'],
                    'resonance' => 'L',
                    'ruleset' => 'core',
                    'skills' => ['computer', 'hacking'],
                    'sleaze' => 'L+3',
                ],
            ]);
    }

    /**
     * Test loading an invalid sprite with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('shadowrun5e.sprites.show', 'not-found'))
            ->assertNotFound();
    }
}
