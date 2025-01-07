<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\AbstractUser as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as SymfonyRedirectResponse;

use function redirect;

class GoogleController extends Controller
{
    /**
     * Handle a successful login from Google.
     */
    public function handleCallback(): RedirectResponse
    {
        /** @var SocialiteUser */
        $social_user = Socialite::driver('google')->user();
        $user = User::where('email', $social_user->email)->first();

        if (null === $user) {
            // The user wasn't found, create a new one.
            $user = User::create([
                'email' => $social_user->email,
                'name' => $social_user->name,
                'password' => 'reset me',
            ]);
        }

        Auth::login($user);
        return redirect('/dashboard');
    }

    /**
     * The user wants to login to Commlink using their Google login.
     */
    public function redirectToGoogle(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }
}
