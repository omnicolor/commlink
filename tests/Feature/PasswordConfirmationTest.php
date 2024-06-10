<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class PasswordConfirmationTest extends TestCase
{
    public function testConfirmPasswordScreenCanBeRendered(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)->get('/confirm-password')->assertOk();
    }

    public function testPasswordCanBeConfirmed(): void
    {
        /** @var User */
        $user = User::factory()->create();

        self::actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function testPasswordIsNotConfirmedWithInvalidPassword(): void
    {
        /** @var User */
        $user = User::factory()->create();
        self::actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors();
    }
}
