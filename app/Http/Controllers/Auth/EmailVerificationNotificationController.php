<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * @psalm-suppress UnusedClass
 */
class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     * @codeCoverageIgnore
     */
    public function store(Request $request): Response | RedirectResponse
    {
        assert(null !== $request->user());
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
