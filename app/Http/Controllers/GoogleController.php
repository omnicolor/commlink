<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Handle a successful login from Google.
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     */
    public function handleCallback(): RedirectResponse
    {
        $socialUser = Socialite::driver('google')->user();
        $user = User::where('email', $socialUser->email)->first();

        if (null === $user) {
            // The user wasn't found, create a new one.
            $user = User::create([
                'email' => $socialUser->email,
                'name' => $socialUser->name,
                'password' => 'reset me',
            ]);
        }

        Auth::login($user);
        return redirect('/dashboard');
    }

    /**
     * The user wants to login to Commlink using their Google login.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }
}
