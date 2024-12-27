<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

use function event;
use function redirect;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     * @codeCoverageIgnore
     */
    public function __invoke(
        EmailVerificationRequest $request
    ): RedirectResponse {
        if (null !== $request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()
                ->intended(RouteServiceProvider::HOME . '?verified=1');
        }

        /** @var MustVerifyEmail&User */
        $user = $request->user();
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->intended(RouteServiceProvider::HOME . '?verified=1');
    }
}
