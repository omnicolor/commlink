<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Mail\Auth\ForgotPassword;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @medium
 */
final class PasswordResetTest extends TestCase
{
    public function testResetPasswordLinkScreenCanBeRendered(): void
    {
        $this->get('/forgot-password')->assertOk();
    }

    public function testResetPasswordLinkCanBeRequested(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        self::post('/forgot-password', ['email' => $user->email]);

        Mail::assertSent(ForgotPassword::class);
    }

    public function testResetPasswordScreenCanBeRendered(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        self::post('/forgot-password', ['email' => $user->email]);

        Mail::assertSent(ForgotPassword::class, function (ForgotPassword $mail): bool {
            self::get('/reset-password/' . $mail->token)
                ->assertOk();
            return true;
        });
    }

    public function testPasswordCanBeResetWithValidToken(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        self::post('/forgot-password', ['email' => $user->email]);

        Mail::assertSent(ForgotPassword::class, function (ForgotPassword $mail) use ($user): bool {
            self::post('/reset-password', [
                'token' => $mail->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
                ->assertSessionHasNoErrors();

            return true;
        });
    }
}
