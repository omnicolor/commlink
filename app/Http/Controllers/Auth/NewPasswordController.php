<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

use function back;
use function redirect;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     * @codeCoverageIgnore
     */
    public function store(Request $request): RedirectResponse
    {
        // @phpstan-ignore-next-line
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);

        // Here we will attempt to reset the user's password. If it is
        // successful we will update the password on an actual user model and
        // persist it to the database. Otherwise we will parse the error and
        // return the response.
        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user) use ($request): void {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user
        // back to the application's home authenticated view. If there is an
        // error we can redirect them back to where they came from with their
        // error message.
        if (Password::PASSWORD_RESET === $status) {
            return redirect()->route('login')->with('status', __($status));
        }

        // Don't give away that the email is or isn't valid...
        if (Password::INVALID_USER === $status) {
            $status = Password::INVALID_TOKEN;
        }
        return back()->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }
}
