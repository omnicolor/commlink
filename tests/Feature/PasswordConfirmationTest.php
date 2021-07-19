<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @medium
 */
final class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function testConfirmPasswordScreenCanBeRendered(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function testPasswordCanBeConfirmed(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    public function testPasswordIsNotConfirmedWithInvalidPassword(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
