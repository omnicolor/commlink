<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

/**
 * @psalm-suppress UnusedClass
 */
class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     * @codeCoverageIgnore
     */
    public function store(Request $request): RedirectResponse
    {
        // @phpstan-ignore-next-line
        $request->validate(['email' => 'required|email']);

        // We will send the password reset link to this user. Once we have
        // attempted to send the link, we will examine the response then see the
        // message we need to show to the user. Finally, we'll send out a proper
        // response.
        $status = Password::sendResetLink($request->only('email'));

        if (Password::RESET_LINK_SENT === $status) {
            return redirect()->route('welcome')->with('status', __($status));
        }
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
