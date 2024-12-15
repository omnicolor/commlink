<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use function redirect;
use function view;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     * @codeCoverageIgnore
     */
    public function __invoke(Request $request): RedirectResponse | View
    {
        if (null !== $request->user() && $request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        return view('auth.verify-email');
    }
}
