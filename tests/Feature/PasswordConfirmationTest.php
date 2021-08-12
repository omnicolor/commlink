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

        $this->actingAs($user)->get('/confirm-password')
            ->assertOk();
    }

    public function testPasswordCanBeConfirmed(): void
    {
        /** @var User */
        $user = User::factory()->create();

        $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function testPasswordIsNotConfirmedWithInvalidPassword(): void
    {
        /** @var User */
        $user = User::factory()->create();
        $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors();
    }
}
