<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * @medium
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function testEmailVerificationScreenCanBeRendered(): void
    {
        /** @var User */
        $user = User::factory()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get('/verify-email')
            ->assertOk();
    }

    public function testEmailCanBeVerified(): void
    {
        Event::fake();

        /** @var User */
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => \sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        // @phpstan-ignore-next-line
        self::assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(RouteServiceProvider::HOME . '?verified=1');
    }

    public function testEmailIsNotVerifiedWithInvalidHash(): void
    {
        /** @var User */
        $user = User::factory()->create(['email_verified_at' => null]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => \sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        // @phpstan-ignore-next-line
        self::assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
