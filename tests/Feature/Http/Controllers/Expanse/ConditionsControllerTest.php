<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\Expanse;

use App\Models\User;
use Illuminate\Http\Response;

/**
 * Tests for the conditions controller.
 * @covers \App\Http\Controllers\Expanse\ConditionsController
 * @group controllers
 * @group expanse
 * @medium
 */
final class ConditionsControllerTest extends \Tests\TestCase
{
    /**
     * Test loading the collection if the config is broken.
     * @test
     */
    public function testIndexBrokenConfig(): void
    {
        \Config::set('app.data_path.expanse', '/tmp/unused/');
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('expanse.conditions.index'))
            ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test loading the collection without authentication.
     * @test
     */
    public function testNoAuthIndex(): void
    {
        $this->getJson(route('expanse.conditions.index'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading the collection as an authenticated user.
     * @test
     */
    public function testAuthIndex(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $response = $this->actingAs($user)
            ->getJson(route('expanse.conditions.index'))
            ->assertOk()
            ->assertJsonFragment([
                'links' => [
                    'self' => '/api/expanse/conditions/deafened',
                ],
            ]);
        self::assertGreaterThanOrEqual(1, \count($response['data']));
    }

    /**
     * Test loading an individual resource without authentication.
     * @test
     */
    public function testNoAuthShow(): void
    {
        $this->getJson(route('expanse.conditions.show', 'deafened'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an invalid resource without authentication.
     * @test
     */
    public function testNoAuthShowNotFound(): void
    {
        $this->getJson(route('expanse.conditions.show', 'not-found'))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test loading an individual resource with authentication.
     * @test
     */
    public function testAuthShow(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
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
     * @test
     */
    public function testAuthShowNotFound(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)
            ->getJson(route('expanse.conditions.show', 'not-found'))
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
}
