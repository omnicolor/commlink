<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignInvitation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

use function abort_if;
use function event;
use function redirect;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function store(Request $request): RedirectResponse
    {
        // @phpstan-ignore-next-line
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
        ]);

        $invitation = null;
        if (isset($request->invitation, $request->token)) {
            /** @var CampaignInvitation */
            $invitation = CampaignInvitation::findOrFail($request->invitation);
            abort_if(
                $invitation->hash() !== $request->token,
                Response::HTTP_FORBIDDEN,
                'The token does not appear to be valid for the invitation',
            );
            abort_if(
                CampaignInvitation::INVITED !== $invitation->status,
                Response::HTTP_BAD_REQUEST,
                'It appears you\'ve already responded to the invitation',
            );
        }
        Auth::login($user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]));

        if (null !== $invitation) {
            /** @var Campaign */
            $campaign = $invitation->campaign;
            $campaign->users()->attach(
                $user->id,
                ['status' => 'accepted']
            );

            $invitation->status = CampaignInvitation::RESPONDED;
            $invitation->responded_at = $invitation->updated_at = now()->toDateTimeString();
            $invitation->save();

            event(new Registered($user));
            return redirect()->route('campaign.view', $campaign);
        }
        event(new Registered($user));

        return redirect(RouteServiceProvider::HOME);
    }
}
