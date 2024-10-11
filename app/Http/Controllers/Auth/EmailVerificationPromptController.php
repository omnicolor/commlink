<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Providers\AppServiceProvider;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * @psalm-suppress UnusedClass
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     * @codeCoverageIgnore
     */
    public function __invoke(Request $request): RedirectResponse | View
    {
        // @phpstan-ignore-next-line
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(AppServiceProvider::HOME);
        }
        return view('auth.verify-email');
    }
}
