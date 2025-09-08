<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\CampaignInvitationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\CampaignInvitation;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('welcome');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        if (isset($request->invitation, $request->token)) {
            /** @var User */
            $user = $request->user();
            /** @var CampaignInvitation */
            $invitation = CampaignInvitation::findOrFail($request->invitation);
            $campaign = $invitation->campaign;

            abort_if(
                $invitation->hash() !== $request->token,
                Response::HTTP_FORBIDDEN,
                'The token does not appear to be valid for the invitation',
            );
            abort_if(
                CampaignInvitationStatus::Invited !== $invitation->status,
                Response::HTTP_BAD_REQUEST,
                'It appears you\'ve already responded to the invitation',
            );
            abort_if(
                $user->is($campaign->gamemaster),
                Response::HTTP_CONFLICT,
                'You can\'t join a game when you\'re the GM',
            );

            $campaign->users()->attach(
                $user->id,
                ['status' => 'accepted']
            );

            $invitation->status = CampaignInvitationStatus::Responded;
            $invitation->responded_at = now()->toDateTimeString();
            $invitation->updated_at = $invitation->responded_at;
            $invitation->save();

            $request->session()->regenerate();
            return redirect()->route('campaign.view', $campaign);
        }
        $request->session()->regenerate();
        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     * @codeCoverageIgnore
     */
    public function destroy(Request $request): RedirectResponse | Redirector
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
