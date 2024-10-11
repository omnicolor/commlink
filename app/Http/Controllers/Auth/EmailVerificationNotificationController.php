<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Providers\AppServiceProvider;
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
        // @phpstan-ignore-next-line
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(AppServiceProvider::HOME);
        }

        // @phpstan-ignore-next-line
        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
