<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Socialite\Facades\Socialite;
use Tests\TestCase;

/**
 * @medium
 */
final class GoogleControllerTest extends TestCase
{
    use RefreshDatabase;
    use WIthFaker;

    /**
     * Test the login with Google redirect.
     * @test
     */
    public function testAuthThroughGoogle(): void
    {
        self::get('/google/auth')
            ->assertRedirectContains('https://accounts.google.com/o/oauth2/auth?');
    }

    /**
     * Test that logging in through Google as a new user creates a User.
     * @test
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

        /** @user */
        $user = User::where('email', $email)->first();
        self::assertNotNull($user);
        self::assertSame($name, $user->name);
        self::assertSame('reset me', $user->password);
        self::assertAuthenticatedAs($user);
    }
}
