<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Expanse;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

use function count;

/**
 * Tests for the social classes controller.
 * @group controllers
 * @group expanse
 * @medium
 */
final class SocialClassesControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.expanse', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.social-classes.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('expanse.social-classes.index'))
            ->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('expanse.social-classes.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route('expanse.social-classes.show', 'middle'),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('expanse.social-classes.show', 'middle'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        self::getJson(route('expanse.social-classes.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.social-classes.show', 'middle'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'id' => 'middle',
                    'name' => 'Middle Class',
                ],
            ]);
    }

    /**
     * Test loading an invalid resource with authentication.
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.social-classes.show', 'not-found'))
            ->assertNotFound();
    }
}
