<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\AppServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

/**
 * @psalm-suppress UnusedClass
 */
class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * @codeCoverageIgnore
     */
    public function __invoke(
        EmailVerificationRequest $request
    ): RedirectResponse {
        // @phpstan-ignore-next-line
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->intended(AppServiceProvider::HOME . '?verified=1');
        }

        /** @var MustVerifyEmail */
        $user = $request->user();
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(AppServiceProvider::HOME . '?verified=1');
    }
}
