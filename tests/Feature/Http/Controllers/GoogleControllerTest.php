<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Socialite\Facades\Socialite;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
final class GoogleControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Test the login with Google redirect.
     */
    public function testAuthThroughGoogle(): void
    {
        self::get('/google/auth')
            ->assertRedirectContains('https://accounts.google.com/o/oauth2/auth?');
    }

    /**
     * Test that logging in through Google as a new user creates a User.
     */
    public function testLoginThroughSlackNewUser(): void
    {
        // Find an email that hasn't been used yet.
        do {
            $email = $this->faker->email();
        } while (null !== User::where('email', $email)->first());

        $name = $this->faker->name();
        Socialite::shouldReceive('driver->user')
            ->andReturn((object)[
                'email' => $email,
                'name' => $name,
            ]);
        self::get('/google/callback')->assertRedirect('/dashboard');

        $user = User::where('email', $email)->first();
        // @phpstan-ignore staticMethod.impossibleType
        self::assertInstanceOf(User::class, $user);
        self::assertSame($name, $user->name);
        self::assertSame('reset me', $user->password);
        self::assertAuthenticatedAs($user);
    }
}
