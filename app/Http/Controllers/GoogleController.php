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

/**
 * @psalm-suppress UnusedClass
 */
class GoogleController extends Controller
{
    /**
     * Handle a successful login from Google.
     */
    public function handleCallback(): RedirectResponse
    {
        /** @var SocialiteUser */
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
    public function redirectToGoogle(): SymfonyRedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }
}
