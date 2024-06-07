<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Expanse;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

use function count;

/**
 * Tests for the conditions controller.
 * @group expanse
 * @medium
 */
final class ConditionsControllerTest extends TestCase
{
    /**
     * Test loading the collection if the config is broken.
     */
    public function testIndexBrokenConfig(): void
    {
        Config::set('app.data_path.expanse', '/tmp/unused/');
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.conditions.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     */
    public function testNoAuthIndex(): void
    {
        self::getJson(route('expanse.conditions.index'))->assertUnauthorized();
    }

    /**
     * Test loading the collection as an authenticated user.
     */
    public function testAuthIndex(): void
    {
        $user = User::factory()->create();
        $response = self::actingAs($user)
            ->getJson(route('expanse.conditions.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => route(
                        'expanse.conditions.show',
                        ['condition' => 'deafened'],
                    ),
                ],
            ]);
        self::assertGreaterThanOrEqual(1, count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     */
    public function testNoAuthShow(): void
    {
        self::getJson(route('expanse.conditions.show', 'deafened'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an invalid resource without authentication.
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('expanse.conditions.show', 'not-found'))
            ->assertUnauthorized();
    }

    /**
     * Test loading an individual resource with authentication.
     */
    public function testAuthShow(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.conditions.show', 'deafened'))
            ->assertOk()
            ->assertJson([
                'data' => [
                    'description' => 'The character cannot hear and '
                        . 'automatically fails ability tests reliant on '
                        . 'hearing, such as Perception (Hearing).',
                    'name' => 'Deafened',
                    'page' => 21,
                ],
            ]);
    }

    /**
     * Test loading an invalid resource with authentication.
     */
    public function testAuthShowNotFound(): void
    {
        $user = User::factory()->create();
        self::actingAs($user)
            ->getJson(route('expanse.conditions.show', 'not-found'))
            ->assertNotFound();
    }
}
