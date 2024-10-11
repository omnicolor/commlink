<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Providers\AppServiceProvider;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Medium]
class EmailVerificationTest extends TestCase
{
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
        self::assertTrue($user->fresh()?->hasVerifiedEmail());
        $response->assertRedirect(AppServiceProvider::HOME . '?verified=1');
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

        self::assertFalse($user->fresh()?->hasVerifiedEmail());
    }
}
