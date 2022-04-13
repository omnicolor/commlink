<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password view.
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request): \Illuminate\View\View
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirm the user's password.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (
            !Auth::guard('web')->validate([
                'email' => optional($request->user())->email,
                'password' => $request->password,
            ])
        ) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', \time());

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
